<a href="{{ url('/categoria/' . $categoria->id) }}" 
                    class="block bg-white rounded-2xl shadow hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 group transform hover:-translate-y-1">
                        
                        {{-- Category name --}}
                        <div class="p-4 pb-0 flex flex-col items-center text-center">
                            <h3 class="text-lg font-bold text-gray-800 group-hover:text-indigo-600 transition">
                                {{ $categoria->nome }}
                            </h3>
                            <div class="w-10 h-1 bg-indigo-500 rounded-full mt-1 mb-3"></div>
                        </div>

                        {{-- Image --}}
                        @if($categoria->thumbnail)
                            <img 
                                src="{{ asset($categoria->thumbnail) }}" 
                                alt="{{ $categoria->nome }}" 
                                class="w-full h-32 object-cover transform group-hover:scale-105 transition duration-300"
                            >
                        @else
                            <div class="w-full h-32 bg-gray-200 flex items-center justify-center text-gray-400">
                                No Image
                            </div>
                        @endif

                        {{-- Description --}}
                        @if($categoria->descricao)
                            <div class="p-4">
                                <p class="text-sm text-gray-600 text-center">
                                    {{ Str::limit($categoria->descricao, 60) }}
                                </p>
                            </div>
                        @endif
                    </a>