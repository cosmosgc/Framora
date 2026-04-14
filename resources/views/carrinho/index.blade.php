@extends('layouts.app')

@section('content')
@php
    $total = $carrinho->fotos->sum(function ($i) {
        return $i->foto->galeria->valor_foto ?? 0;
    });
@endphp

<div class="space-y-8">
    <section class="app-shell px-6 py-8 sm:px-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl space-y-3">
                <p class="page-eyebrow">Checkout</p>
                <h1 class="page-title">Seu carrinho está pronto para fechar a compra.</h1>
                <p class="page-copy">
                    Revise as fotos selecionadas, confira o total e escolha a forma de pagamento antes de seguir para o checkout.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <div class="stat-pill">
                    <span>{{ $carrinho->fotos->count() }}</span>
                    <span class="text-stone-500">itens</span>
                </div>
                <div class="stat-pill">
                    <span>R$ {{ number_format($total, 2, ',', '.') }}</span>
                    <span class="text-stone-500">total</span>
                </div>
            </div>
        </div>
    </section>

    <div class="space-y-3">
        @if(session('success'))
            <div class="app-alert app-alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="app-alert app-alert-error">{{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="app-alert app-alert-info">{{ session('info') }}</div>
        @endif
    </div>

    @if($carrinho->fotos->isEmpty())
        <section class="empty-state">
            <p class="page-eyebrow">Carrinho vazio</p>
            <h2 class="mt-3 text-2xl font-semibold text-stone-950">Nenhuma foto foi adicionada ainda.</h2>
            <p class="mx-auto mt-3 max-w-2xl page-copy">
                Quando você selecionar imagens nas galerias, elas vão aparecer aqui com o total consolidado para checkout.
            </p>
            <a href="{{ route('galerias.web.index') }}" class="btn-primary mt-6">Explorar galerias</a>
        </section>
    @else
        <div class="grid gap-8 xl:grid-cols-[minmax(0,1fr)_360px]">
            <section class="space-y-4">
                @foreach($carrinho->fotos as $item)
                    <article class="app-panel overflow-hidden p-4 sm:p-5">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            <div class="h-28 w-full overflow-hidden rounded-[1.5rem] bg-stone-100 sm:w-32">
                                <img
                                    src="{{ $item->foto->caminho_thumb ?? $item->foto->caminho_foto ?? '#' }}"
                                    alt=""
                                    class="h-full w-full object-cover"
                                >
                            </div>

                            <div class="flex-1 space-y-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-cyan-800">
                                        Foto
                                    </span>
                                    <span class="text-sm text-stone-500">ID {{ $item->foto->id ?? 'sem identificação' }}</span>
                                </div>

                                <h3 class="text-xl font-semibold text-stone-950">
                                    {{ $item->foto->galeria->nome ?? 'Galeria sem nome' }}
                                </h3>

                                <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-stone-600">
                                    <p>Preço: <span class="font-semibold text-stone-900">R$ {{ number_format($item->foto->galeria->valor_foto ?? 0, 2, ',', '.') }}</span></p>
                                    <p>Arquivo: {{ basename($item->foto->caminho_foto ?? $item->foto->caminho_thumb ?? 'imagem') }}</p>
                                </div>
                            </div>

                            <form action="{{ route('carrinho.destroy', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger w-full sm:w-auto">Remover</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </section>

            <aside class="space-y-5">
                <section class="app-panel sticky top-6 overflow-hidden">
                    <div class="border-b border-stone-100 px-6 py-5">
                        <p class="page-eyebrow !text-emerald-700">Resumo</p>
                        <h2 class="mt-2 text-2xl font-semibold tracking-tight text-stone-950">Finalizar compra</h2>
                    </div>

                    <div class="space-y-5 p-6">
                        <div class="app-panel-muted p-5">
                            <div class="flex items-center justify-between text-sm text-stone-600">
                                <span>Itens selecionados</span>
                                <span class="font-semibold text-stone-900">{{ $carrinho->fotos->count() }}</span>
                            </div>
                            <div class="mt-3 flex items-center justify-between text-base text-stone-700">
                                <span>Total</span>
                                <span class="text-2xl font-semibold text-stone-950">R$ {{ number_format($total, 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <form id="checkoutForm" action="{{ route('stripe.checkout', $carrinho->id) }}" method="POST" class="space-y-4">
                            @csrf

                            <div>
                                <label for="forma_pagamento" class="field-label">Forma de pagamento</label>
                                <select name="forma_pagamento" id="forma_pagamento" required class="field-input">
                                    <option value="pix">PIX</option>
                                    <option value="cartao">Cartão</option>
                                    <option value="boleto">Boleto</option>
                                </select>
                            </div>

                            <button id="checkoutBtn" type="submit" class="btn-primary w-full">
                                Finalizar compra
                                <span id="checkoutSpinner" class="hidden">...</span>
                            </button>
                        </form>

                        <div class="rounded-[1.5rem] border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-900/80">
                            Você será redirecionado para a sessão de pagamento assim que o checkout for iniciado com sucesso.
                        </div>

                        <div id="checkoutMessage" class="text-sm"></div>
                    </div>
                </section>
            </aside>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('checkoutForm');
    if (!form) {
        return;
    }

    const btn = document.getElementById('checkoutBtn');
    const spinner = document.getElementById('checkoutSpinner');
    const message = document.getElementById('checkoutMessage');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        message.innerHTML = '';
        btn.disabled = true;
        spinner.classList.remove('hidden');

        const formaPagamento = document.getElementById('forma_pagamento').value;

        try {
            const endpoint = "{{ route('stripe.api.checkout',$carrinho->id) }}";

            const resp = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    forma_pagamento: formaPagamento
                })
            });

            if (!resp.ok) {
                const errObj = await resp.json().catch(async () => {
                    const txt = await resp.text().catch(() => 'Erro desconhecido');
                    return { message: txt };
                });
                throw new Error(errObj.message || 'Erro ao criar sessão de pagamento');
            }

            const data = await resp.json();

            if (data.url) {
                window.location.href = data.url;
            } else {
                throw new Error('Resposta inválida do servidor (sem url)');
            }
        } catch (err) {
            console.error(err);
            message.innerHTML = `<div class="app-alert app-alert-error">${err.message}</div>`;
            btn.disabled = false;
            spinner.classList.add('hidden');
        }
    });
});
</script>
@endsection
