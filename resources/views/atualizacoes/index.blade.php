@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
  <header class="mb-6">
    <h1 class="text-3xl font-extrabold tracking-tight">Atualizações do Repositório</h1>
    <p class="mt-1 text-sm text-slate-500">Commits e Pull Requests recentes — PRs destacados como features em desenvolvimento.</p>
  </header>

  {{-- Legenda / Status PR summary --}}
  <section class="mb-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- PR: Open --}}
    <div class="bg-white shadow rounded-lg p-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-sm font-semibold text-slate-700">Features em desenvolvimento</h3>
          <p class="mt-1 text-xs text-slate-400">PRs abertos aguardando revisão/merge.</p>
        </div>
        <div class="text-sm font-medium inline-flex items-center gap-2">
          <span class="px-2 py-1 rounded-md text-xs font-semibold bg-amber-100 text-amber-800">open</span>
        </div>
      </div>

      @php
        // transformação rápida (melhor mover pro Controller)
        $prCollection = collect($prs)->map(function($p){
          return [
            'title' => data_get($p,'title'),
            'number' => data_get($p,'number'),
            'author' => data_get($p,'user.login'),
            'url' => data_get($p,'html_url'),
            'created_at' => data_get($p,'created_at'),
            'merged' => data_get($p,'merged'),
            'state' => data_get($p,'state'),
            'body' => data_get($p,'body'),
            'head_ref' => data_get($p,'head.ref'),
            'changed_files' => data_get($p,'changed_files'),
            'commits' => data_get($p,'commits'),
          ];
        });

        $openPRs = $prCollection->filter(fn($x)=> ($x['merged'] !== true) && ($x['state'] === 'open'))->values();
        $mergedPRs = $prCollection->filter(fn($x)=> $x['merged'] === true)->values();
        $closedPRs = $prCollection->filter(fn($x)=> ($x['state'] === 'closed' && $x['merged'] !== true))->values();
      @endphp

      <div class="mt-4 space-y-3">
        @forelse($openPRs->take(5) as $pr)
          <a href="{{ $pr['url'] }}" target="_blank" class="block p-3 rounded-md hover:bg-slate-50">
            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <div class="text-sm font-semibold text-slate-800 truncate">{{ $pr['title'] }}</div>
                <div class="text-xs text-slate-400 truncate">#{{ $pr['number'] }} · {{ $pr['author'] }} · {{ \Carbon\Carbon::parse($pr['created_at'])->diffForHumans() }}</div>
              </div>
              <div class="text-xs">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-amber-800 bg-amber-100 font-medium">open</span>
              </div>
            </div>
          </a>
        @empty
          <div class="text-xs text-slate-400">Nenhum PR aberto.</div>
        @endforelse
      </div>
    </div>

    {{-- PR: Merged --}}
    <div class="bg-white shadow rounded-lg p-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-sm font-semibold text-slate-700">Features mergeadas</h3>
          <p class="mt-1 text-xs text-slate-400">PRs que já foram integradas ao main.</p>
        </div>
        <div class="text-sm font-medium inline-flex items-center gap-2">
          <span class="px-2 py-1 rounded-md text-xs font-semibold bg-violet-100 text-violet-800">merged</span>
        </div>
      </div>

      <div class="mt-4 space-y-3">
        @forelse($mergedPRs->take(5) as $pr)
          <a href="{{ $pr['url'] }}" target="_blank" class="block p-3 rounded-md hover:bg-slate-50">
            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <div class="text-sm font-semibold text-slate-800 truncate">{{ $pr['title'] }}</div>
                <div class="text-xs text-slate-400 truncate">#{{ $pr['number'] }} · {{ $pr['author'] }} · {{ \Carbon\Carbon::parse($pr['created_at'])->diffForHumans() }}</div>
              </div>
              <div class="text-xs">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-violet-800 bg-violet-100 font-medium">merged</span>
              </div>
            </div>
          </a>
        @empty
          <div class="text-xs text-slate-400">Nenhuma feature mergeada recentemente.</div>
        @endforelse
      </div>
    </div>

    {{-- PR: Closed --}}
    <div class="bg-white shadow rounded-lg p-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-sm font-semibold text-slate-700">PRs fechadas / rejeitadas</h3>
          <p class="mt-1 text-xs text-slate-400">PRs encerradas sem merge.</p>
        </div>
        <div class="text-sm font-medium inline-flex items-center gap-2">
          <span class="px-2 py-1 rounded-md text-xs font-semibold bg-rose-100 text-rose-800">closed</span>
        </div>
      </div>

      <div class="mt-4 space-y-3">
        @forelse($closedPRs->take(5) as $pr)
          <a href="{{ $pr['url'] }}" target="_blank" class="block p-3 rounded-md hover:bg-slate-50">
            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <div class="text-sm font-semibold text-slate-800 truncate">{{ $pr['title'] }}</div>
                <div class="text-xs text-slate-400 truncate">#{{ $pr['number'] }} · {{ $pr['author'] }} · {{ \Carbon\Carbon::parse($pr['created_at'])->diffForHumans() }}</div>
              </div>
              <div class="text-xs">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-rose-800 bg-rose-100 font-medium">closed</span>
              </div>
            </div>
          </a>
        @empty
          <div class="text-xs text-slate-400">Nenhuma PR fechada recentemente.</div>
        @endforelse
      </div>
    </div>
  </section>

  {{-- Timeline principal (commits + PRs) --}}
  @php
    // transformar commits e PRs em items padronizados (idealmente faça isso no Controller)
    function detectTypeFromMessage($msg) {
      if (!$msg) return 'chore';
      $m = strtolower($msg);
      if (str_starts_with($m, 'merge') || str_contains($m, 'merge branch')) return 'merge';
      if (str_starts_with($m, 'fix') || str_contains($m, 'bugfix') || str_contains($m, 'fix:')) return 'fix';
      if (str_starts_with($m, 'feat') || str_contains($m, 'feature') || str_contains($m, 'add')) return 'feat';
      if (str_starts_with($m, 'tweak') || str_contains($m, 'tweak') || str_contains($m, 'adjust')) return 'tweak';
      if (str_starts_with($m, 'docs') || str_contains($m, 'readme') || str_contains($m, 'doc')) return 'docs';
      return 'chore';
    }

    $commitItems = collect($commits)->map(function($c){
      $message = data_get($c, 'commit.message', '');
      $firstLine = preg_split("/\r\n|\n|\r/", $message)[0] ?? $message;
      return [
        'kind'=>'commit',
        'type'=> detectTypeFromMessage($firstLine),
        'title'=> $firstLine,
        'message'=> $message,
        'author'=> data_get($c,'commit.author.name') ?? data_get($c,'author.login'),
        'date'=> data_get($c,'commit.author.date') ?? now()->toIso8601String(),
        'url'=> data_get($c,'html_url'),
        'hash'=> data_get($c,'sha'),
        'branch'=> null,
        'meta'=> ['files'=> null]
      ];
    });

    $prItems = collect($prs)->map(function($p){
      $title = data_get($p,'title','PR');
      return [
        'kind'=>'pr',
        'type'=> detectTypeFromMessage($title),
        'title'=> $title,
        'message'=> data_get($p,'body'),
        'author'=> data_get($p,'user.login'),
        'date'=> data_get($p,'created_at') ?? now()->toIso8601String(),
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
  @endphp

  <main class="space-y-6">
    <ul class="relative border-l border-slate-200">
      @foreach($items as $item)
        <li class="mb-6 ml-6">
          @if($item['kind'] === 'commit')
            <x-commit-card :item="$item" />
          @else
            <x-pr-card :item="$item" />
          @endif
        </li>
      @endforeach

      @if($items->isEmpty())
        <li class="mb-6 ml-6">
          <div class="bg-white p-4 rounded-xl shadow-sm ring-1 ring-slate-100 text-slate-500">
            Nenhuma atualização encontrada.
          </div>
        </li>
      @endif
    </ul>
  </main>

  <footer class="mt-8 flex items-center justify-between text-sm text-slate-500">
    <div>Mostrando <strong class="text-slate-700">{{ $items->count() }}</strong> atualizações</div>
    <div class="flex items-center gap-3">
      <a href="{{ route(("updates.update")) }}"><button id="refreshBtn" class="px-3 py-1 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm">Atualizar</button></a>
      <small class="text-xs text-slate-400">Dica: mova essa transformação para o Controller para performance.</small>
    </div>
  </footer>
</div>
@endsection
