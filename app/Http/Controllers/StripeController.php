<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use App\Models\Carrinho;
use App\Models\Pedido;
use App\Models\Inventario;

class StripeController extends Controller
{
    public function __construct()
    {
        // nada por enquanto
    }

    /**
     * Cria uma Stripe Checkout Session para o carrinho $id.
     * Retorna JSON com url para redirecionar (ideal para SPA/mobile).
     */
    public function createCheckoutSession(Request $request, $id)
    {
        
        $carrinho = Carrinho::with('fotos.foto.galeria')->find($id);
        if (! $carrinho) {
            return response()->json(['message' => 'Carrinho não encontrado'], 404);
        }
        
        if ($carrinho->user_id !== Auth::id()) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        if ($carrinho->fotos->isEmpty()) {
            return response()->json(['message' => 'Carrinho vazio'], 400);
        }

        $request->validate([
            'forma_pagamento' => 'required|string',
        ]);
        // inicializa stripe
        Stripe::setApiKey(config('services.stripe.secret'));
        $currency = strtolower(config('services.stripe.currency', 'brl'));

        DB::beginTransaction();
        try {
            // Cria Pedido com status pendente para referenciar depois
            $valorTotal = $carrinho->fotos->sum(function ($item) {
                $preco = /*$item->preco ?? */($item->foto->galeria->valor_foto ?? 0);
                $quantidade = max(1, (int)($item->quantidade ?? 1));
                return $preco * $quantidade;
            });

            $pedido = Pedido::create([
                'user_id' => $carrinho->user_id,
                'carrinho_id' => $carrinho->id,
                'status_pedido' => 'pendente',
                'forma_pagamento' => $request->input('forma_pagamento'),
                'valor_total' => $valorTotal,
            ]);

            // Monta line_items para Stripe (valor em centavos)
            $line_items = [];
            foreach ($carrinho->fotos as $item) {
                $preco = /*$item->preco ??*/ ($item->foto->galeria->valor_foto ?? 0);
                $quantidade = max(1, (int)($item->quantidade ?? 1));
                $unit_amount = (int) round($preco * 100); // centavos

                $name = $item->foto->nome ?? ('Foto #' . ($item->foto_id ?? ''));
                $description = $item->foto->galeria->nome ?? null;

                $line_items[] = [
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => $name,
                            'description' => $description,
                        ],
                        'unit_amount' => $unit_amount,
                    ],
                    'quantity' => $quantidade,
                ];
            }

            // Cria sessão de checkout
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $line_items,
                'mode' => 'payment',
                'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.cancel'),
                'metadata' => [
                    'pedido_id' => $pedido->id,
                    'carrinho_id' => $carrinho->id,
                    'user_id' => $carrinho->user_id,
                ],
            ]);

            // Salva o session id no pedido para referência posterior
            $pedido->stripe_session_id = $session->id;
            $pedido->save();

            // salva session id e commit já feito acima...

            DB::commit();

            // Se a requisição quer JSON (ex: chamada fetch/ajax), retorna JSON com a url
            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json(['url' => $session->url, 'pedido' => $pedido], 201);
            }

            // Requisição web normal (form submit): redireciona diretamente para a Stripe Checkout
            return redirect()->away($session->url);


        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stripe checkout error: '.$e->getMessage());
            return response()->json(['message' => 'Erro ao criar sessão de pagamento'.$e->getMessage()], 500);
        }
    }

    /**
     * Página de sucesso: usa session_id para confirmar e finalizar o pedido.
     * Pode ser chamada via redirect do Stripe Checkout.
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        if (! $sessionId) {
            return redirect()->route('home')->with('error', 'Sessão Stripe não informada.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = Session::retrieve($sessionId);

            // Verifica se já finalizamos esse pedido
            $pedidoId = $session->metadata->pedido_id ?? null;
            if (! $pedidoId) {
                // tenta buscar pelo session_id no banco
                $pedido = Pedido::where('stripe_session_id', $sessionId)->first();
            } else {
                $pedido = Pedido::find($pedidoId);
            }

            if (! $pedido) {
                Log::warning("Pedido não encontrado para session {$sessionId}");
                return redirect()->route('home')->with('error', 'Pedido não encontrado.');
            }

            // Confirma pagamento: depende do payment_status do session
            // payment_status pode ser 'paid' ou 'unpaid' — veja docs stripe
            if ($session->payment_status === 'paid' && $pedido->status_pedido !== 'pago') {
                DB::beginTransaction();
                try {
                    // marca pedido como pago
                    $pedido->status_pedido = 'pago';
                    $pedido->payment_intent = $session->payment_intent ?? null;
                    $pedido->save();

                    // cria inventario (recria a lógica que você tinha)
                    $carrinho = Carrinho::with('fotos')->find($pedido->carrinho_id);
                    if ($carrinho) {
                        foreach ($carrinho->fotos as $item) {
                            Inventario::create([
                                'user_id' => $pedido->user_id,
                                'foto_id' => $item->foto_id,
                                'pedido_id' => $pedido->id,
                            ]);
                        }
                        // remover itens do carrinho
                        $carrinho->fotos()->delete();
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Erro finalizando pedido após sucesso Stripe: " . $e->getMessage());
                    return redirect()->route('home')->with('error', 'Erro ao finalizar pedido após pagamento.');
                }
            }

            // Redireciona para inventário do usuário (ajuste a rota conforme sua app)
            return redirect()->route('inventario.index')->with('success', 'Pagamento confirmado! Pedido finalizado.');

        } catch (\Exception $e) {
            Log::error('Stripe success error: '.$e->getMessage());
            return redirect()->route('home')->with('error', 'Erro ao confirmar pagamento.');
        }
    }

    public function cancel()
    {
        return view('stripe.cancel'); // crie view simples informando que usuário cancelou
    }

    /**
     * Webhook handler (recomendado)
     * Configure no painel Stripe a URL /stripe/webhook com o signing secret.
     * Esse endpoint valida a assinatura e processa events (p.ex. checkout.session.completed).
     */
    public function webhook(Request $request)
    {
        // corpo cru e header
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret') ?? env('STRIPE_WEBHOOK_SECRET');

        // Se webhooks não configurados, rejeite (opcional)
        if (empty($webhookSecret)) {
            Log::error('Stripe webhook secret not configured.');
            return response()->json(['error' => 'Webhook not configured'], 500);
        }

        try {
            // valida assinatura e desserializa o evento
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            // payload inválido
            Log::warning('Stripe webhook: invalid payload - ' . $e->getMessage());
            return response()->json(['message' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // assinatura inválida
            Log::warning('Stripe webhook: invalid signature - ' . $e->getMessage());
            return response()->json(['message' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Stripe webhook unexpected error: ' . $e->getMessage());
            return response()->json(['message' => 'Webhook error'], 500);
        }

        $type = $event->type;
        Log::info("Stripe webhook received: {$type}");

        // Despachar tipos relevantes
        try {
            if ($type === 'checkout.session.completed') {
                $session = $event->data->object;

                // metadata com pedido_id que criamos na sessão
                $pedidoId = $session->metadata->pedido_id ?? null;

                if (! $pedidoId) {
                    Log::warning("Webhook checkout.session.completed sem metadata pedido_id. Session id: {$session->id}");
                } else {
                    DB::beginTransaction();
                    try {
                        $pedido = \App\Models\Pedido::lockForUpdate()->find($pedidoId); // lock para evitar race
                        if (! $pedido) {
                            Log::warning("Pedido {$pedidoId} não encontrado no webhook.");
                        } else {
                            if ($pedido->status_pedido === 'pago') {
                                // já processado — idempotência
                                Log::info("Pedido {$pedidoId} já está marcado como pago. Ignorando.");
                            } else {
                                // marca pedido como pago e salva dados do pagamento
                                $pedido->status_pedido = 'pago';
                                $pedido->payment_intent = $session->payment_intent ?? null;
                                $pedido->stripe_session_id = $session->id;
                                $pedido->save();

                                // criar inventário e limpar carrinho
                                $carrinho = \App\Models\Carrinho::with('fotos')->find($pedido->carrinho_id);
                                if ($carrinho) {
                                    foreach ($carrinho->fotos as $item) {
                                        \App\Models\Inventario::create([
                                            'user_id' => $pedido->user_id,
                                            'foto_id' => $item->foto_id,
                                            'pedido_id' => $pedido->id,
                                        ]);
                                    }
                                    // remove os itens (assumindo relacionamento pivot com método fotos())
                                    $carrinho->fotos()->delete();
                                }
                            }
                        }

                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error('Erro ao processar checkout.session.completed: ' . $e->getMessage());
                        return response()->json(['message' => 'Erro interno'], 500);
                    }
                }
            }

            // Exemplo: tratar payment_intent.succeeded se você usa Payment Intents diretamente
            if ($type === 'payment_intent.succeeded') {
                $intent = $event->data->object;
                Log::info("payment_intent.succeeded received: {$intent->id}");
                // aqui você poderia encontrar o pedido por payment_intent e marcar como pago
                // (implemente conforme necessidade)
            }

            // outros eventos que queira tratar...
        } catch (\Exception $e) {
            Log::error('Erro no processamento do webhook: ' . $e->getMessage());
            return response()->json(['message' => 'Erro interno'], 500);
        }

        // Acknowledge
        return response()->json(['received' => true], 200);
    }
}
