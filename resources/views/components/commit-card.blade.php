@props(['item'])

@php
  $badgeClasses = [
    'merge' => 'bg-violet-100 text-violet-800',
    'fix' => 'bg-rose-100 text-rose-800',
    'tweak' => 'bg-amber-100 text-amber-800',
    'feat' => 'bg-emerald-100 text-emerald-800',
    'docs' => 'bg-sky-100 text-sky-800',
    'chore' => 'bg-slate-100 text-slate-700',
  ];

  $badge = $badgeClasses[$item['type']] ?? $badgeClasses['chore'];
  $timeReadable = \Carbon\Carbon::parse($item['date'])->diffForHumans();
@endphp

<div class="relative bg-white p-4 rounded-xl shadow-sm ring-1 ring-slate-100 hover:shadow-md transition">
  <div class="flex items-start justify-between gap-4">
    <div class="flex-1 min-w-0">
      <div class="flex items-baseline gap-3">
        <h3 class="text-slate-900 font-semibold truncate">{{ $item['title'] }}</h3>
        @if(!empty($item['hash']))
          <span class="text-xs text-slate-400">commit {{ \Illuminate\Support\Str::limit($item['hash'], 7) }}</span>
        @endif
      </div>

      @if(!empty($item['message']))
        <p class="mt-2 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($item['message'], 220) }}</p>
      @endif

      <div class="mt-3 flex items-center gap-3 text-xs text-slate-500">
        <span class="inline-flex items-center gap-2">
          <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A8 8 0 1118.88 6.196 8 8 0 015.121 17.804z"></path></svg>
          {{ $item['author'] }}
        </span>

        <span>•</span>

        <time datetime="{{ $item['date'] }}" class="text-slate-400">{{ $timeReadable }}</time>

        <span>•</span>

        <span class="inline-flex items-center gap-2 px-2 py-0.5 rounded-md text-xs font-medium {{ $badge }}">{{ $item['type'] }}</span>

        <a href="{{ $item['url'] }}" target="_blank" class="ml-2 text-sky-600 hover:underline">ver commit ↗</a>
      </div>
    </div>

    <div class="hidden sm:flex sm:flex-col sm:items-end sm:gap-2 text-xs text-slate-500">
      <div>branch: <strong class="text-slate-700">{{ $item['branch'] ?? '—' }}</strong></div>
      <div>
        @if(isset($item['meta']['files']))
          {{ $item['meta']['files'] }} arquivos
        @else
          —
        @endif
      </div>
    </div>
  </div>
</div>
