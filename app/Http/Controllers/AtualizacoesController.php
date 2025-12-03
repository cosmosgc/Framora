<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use League\CommonMark\CommonMarkConverter;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AtualizacoesController extends Controller
{
    protected string $repo = env('GIT_REPO', 'cosmosgc/Framora'); // proprietário/repo
    protected string $basePath = 'updates'; // storage/app/atualizacoes

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Se quiser forçar atualização pela query string: /atualizacoes?refresh=1
        $force = $request->boolean('refresh', false);

        // arquivos locais
        $commitsFile = "{$this->basePath}/commits.json";
        $prsFile = "{$this->basePath}/prs.json";
        $itemsFile = "{$this->basePath}/items.json";

        if ($force || !Storage::exists($commitsFile) || !Storage::exists($prsFile)) {
            // se for forçar, ou arquivos não existem -> busca e grava
            $this->fetchAndSave();
        }

        // lê os arquivos locais (espera JSON)
        $commits = json_decode(Storage::get($commitsFile), true) ?? [];
        $prs = json_decode(Storage::get($prsFile), true) ?? [];

        // converter os itens (mesma lógica que você tinha) — aqui reaplicamos o CommonMark
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $detectType = function($msg) {
            if (!$msg) return 'chore';
            $m = strtolower($msg);
            if (str_starts_with($m, 'merge') || str_contains($m, 'merge branch')) return 'merge';
            if (str_starts_with($m, 'fix') || str_contains($m, 'bugfix') || str_contains($m, 'fix:')) return 'fix';
            if (str_starts_with($m, 'feat') || str_contains($m, 'feature') || str_contains($m, 'add')) return 'feat';
            if (str_starts_with($m, 'tweak') || str_contains($m, 'tweak') || str_contains($m, 'adjust')) return 'tweak';
            if (str_starts_with($m, 'docs') || str_contains($m, 'readme') || str_contains($m, 'doc')) return 'docs';
            return 'chore';
        };

        $commitItems = collect($commits)->map(function($c) use ($detectType) {
            $message = data_get($c, 'commit.message', '');
            $firstLine = preg_split("/\r\n|\n|\r/", $message)[0] ?? $message;
            return [
                'kind'=>'commit',
                'type'=> $detectType($firstLine),
                'title'=> $firstLine,
                'message'=> $message,
                'author'=> data_get($c,'commit.author.name') ?? data_get($c,'author.login'),
                'date'=> data_get($c,'commit.author.date') ?? Carbon::now()->toIso8601String(),
                'url'=> data_get($c,'html_url'),
                'hash'=> data_get($c,'sha'),
                'branch'=> null,
                'meta'=> ['files'=> null],
            ];
        });

        $prItems = collect($prs)->map(function($p) use ($converter, $detectType) {
            $title = data_get($p,'title','PR');
            $body = data_get($p,'body','') ?? '';

            // converter para HTML e garantir que seja string
            $rendered = $body ? $converter->convertToHtml($body) : '';
            $htmlDescription = $rendered ? (string) $rendered : '';

            return [
                'kind'=>'pr',
                'type'=> $detectType($title),
                'title'=> $title,
                'message'=> $body,
                'html_description' => $htmlDescription,
                'author'=> data_get($p,'user.login'),
                'date'=> data_get($p,'created_at') ?? Carbon::now()->toIso8601String(),
                'url'=> data_get($p,'html_url'),
                'hash'=> null,
                'branch'=> data_get($p,'head.ref'),
                'meta'=> [
                    'files'=> data_get($p,'changed_files'),
                    'commits_count' => data_get($p,'commits'),
                    'merged' => data_get($p,'merged'),
                    'state' => data_get($p,'state')
                ]
            ];
        });

        $items = $commitItems->concat($prItems)->sortByDesc(fn($i)=> $i['date'])->values();

        // opcional: salvar items processados localmente
        try {
            Storage::put($itemsFile, json_encode($items->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } catch (\Throwable $e) {
            // se falhar aqui, não quebra a exibição
        }
        // dd($prs);
        return view('atualizacoes.index', compact('items', 'commits', 'prs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
        $this->fetchAndSave();

        // redireciona para a página principal com flash
        return redirect()->route('updates.index')->with('status', 'Atualizações buscadas do GitHub e salvas localmente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    protected function fetchAndSave(): void
    {
        $commitsFile = "{$this->basePath}/commits.json";
        $prsFile = "{$this->basePath}/prs.json";

        // cria diretório caso não exista (Storage usa disco 'local' por padrão = storage/app)
        if (!Storage::exists($this->basePath)) {
            Storage::makeDirectory($this->basePath);
        }

        // se tiver token (recomendado para aumentar limite), use-o
        $client = Http::withHeaders([
            'Accept' => 'application/vnd.github.v3+json',
        ]);

        if (env('GITHUB_TOKEN')) {
            $client = $client->withToken(env('GITHUB_TOKEN'));
        }

        try {
            $commitsResp = $client->get("https://api.github.com/repos/{$this->repo}/commits");
            $prsResp = $client->get("https://api.github.com/repos/{$this->repo}/pulls?state=all");

            // opcional: checar status
            if ($commitsResp->ok()) {
                Storage::put($commitsFile, $commitsResp->body());
            }

            if ($prsResp->ok()) {
                Storage::put($prsFile, $prsResp->body());
            }

        } catch (\Throwable $e) {
            // não deixa quebrar: logue e retorne sem salvar
            logger()->error("Erro ao buscar GitHub: " . $e->getMessage());
        }
    }
}
