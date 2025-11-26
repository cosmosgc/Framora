<!-- Modal: viewer (Tailwind) -->
<div id="galleryModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70">
  <div class="relative w-[95%] md:w-3/4 lg:w-2/3 max-h-[90vh] bg-neutral-900 rounded">
    <div class="flex items-center justify-between p-3 border-b border-neutral-700">
      <div id="galleryTitle" class="text-white text-sm"></div>
      <div class="flex items-center gap-2">
        <button id="zoomToggle" class="px-2 py-1 bg-white/10 text-white text-sm rounded">Zoom</button>
        <button id="downloadBtn" class="px-2 py-1 bg-white/10 text-white text-sm rounded">Download</button>
        <button id="closeModalBtn" class="px-2 py-1 bg-white/10 text-white text-sm rounded">Fechar</button>
      </div>
    </div>

    <div class="flex items-center justify-center p-3 relative">
      <button id="prevBtn" class="absolute left-3 top-1/2 -translate-y-1/2 bg-white/10 text-white px-2 py-1 rounded">◀</button>
      <div id="galleryImageContainer" class="max-h-[70vh] overflow-hidden flex items-center justify-center w-full">
        <img id="galleryImage" src="" alt="" class="max-w-full max-h-[70vh] transition-transform duration-150" />
      </div>
      <button id="nextBtn" class="absolute right-3 top-1/2 -translate-y-1/2 bg-white/10 text-white px-2 py-1 rounded">▶</button>
    </div>

    <div class="p-3 text-xs text-neutral-300 border-t border-neutral-700 flex items-center justify-between">
      <div id="galleryMeta"></div>
      <div id="galleryPos" class="text-neutral-400 text-xs"></div>
    </div>
  </div>
</div>
