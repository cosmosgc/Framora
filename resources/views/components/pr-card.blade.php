@props(['item'])

@php
  $typeClasses = [
    'merge' => 'bg-violet-100 text-violet-800',
    'fix' => 'bg-rose-100 text-rose-800',
    'tweak' => 'bg-amber-100 text-amber-800',
    'feat' => 'bg-emerald-100 text-emerald-800',
    'docs' => 'bg-sky-100 text-sky-800',
    'chore' => 'bg-slate-100 text-slate-700',
  ];

  $status = data_get($item,'meta.merged') ? 'merged' : (data_get($item,'meta.state') === 'open' ? 'open' : 'closed');

  $statusClasses = [
    'open' => 'bg-amber-100 text-amber-800',
    'merged' => 'bg-violet-100 text-violet-800',
    'closed' => 'bg-rose-100 text-rose-800',
  ];

  $typeBadge = $typeClasses[$item['type']] ?? $typeClasses['chore'];
  $statusBadge = $statusClasses[$status] ?? $statusClasses['closed'];
  $timeReadable = \Carbon\Carbon::parse($item['date'])->diffForHumans();
@endphp

<div class="relative bg-white p-5 rounded-xl shadow-sm ring-1 ring-slate-100 hover:shadow-md transition">
  <div class="flex items-start gap-4">
    <div class="flex-shrink-0 mt-1">
      <div class="w-12 h-12 rounded-lg bg-sky-50 flex items-center justify-center ring-1 ring-slate-100">
        <svg class="w-6 h-6 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 8c-1.657 0-3 1.343-3 3 0 .738.262 1.417.693 1.95L7 18l3.05-2.693A2.99 2.99 0 0012 20c1.657 0 3-1.343 3-3s-1.343-3-3-3z"/>
        </svg>
      </div>
    </div>

    <div class="flex-1 min-w-0">
      <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
          <div class="flex items-center gap-3">
            <h3 class="text-lg font-semibold text-slate-900 truncate">{{ $item['title'] }}</h3>
            <span class="text-xs text-slate-400">PR</span>
            <span class="inline-flex items-center gap-2 px-2 py-0.5 rounded-md text-xs font-medium {{ $typeBadge }}">
              {{ $item['type'] }}
            </span>
          </div>

          {{-- rendered markdown HTML (safe because we converted with CommonMark + strip) --}}
          @if(!empty($item['html_description']))
            <div class="mt-3 prose prose-sm max-w-none prose-slate">
              {!! $item['html_description'] !!}
            </div>
            {{-- optionally show "view on GitHub" link if too long --}}
          @else
            <p class="mt-3 text-sm text-slate-400 italic">(Sem descrição)</p>
          @endif
        </div>

        <div class="flex flex-col items-end gap-2 text-xs">
          <span class="inline-flex items-center gap-2 px-2 py-0.5 rounded-md font-medium {{ $statusBadge }}">
            {{ $status }}
          </span>
          <div class="text-slate-400">{{ $timeReadable }}</div>
        </div>
      </div>

      <div class="mt-4 flex items-center gap-4 text-xs text-slate-500">
        <div class="inline-flex items-center gap-2">
          <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5.121 17.804A8 8 0 1118.88 6.196 8 8 0 015.121 17.804z"></path>
          </svg>
          {{ $item['author'] }}
        </div>

        <span>•</span>

        <div>Branch: <strong class="text-slate-700">{{ $item['branch'] ?? '—' }}</strong></div>

        <span>•</span>

        <div>{{ $item['meta']['commits_count'] ?? '—' }} commits</div>

        <a href="{{ $item['url'] }}" target="_blank" class="ml-3 text-sky-600 hover:underline">ver PR ↗</a>
      </div>
    </div>
  </div>
</div>
