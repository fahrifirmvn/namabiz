{{-- Multi-file document picker. Inputs only; the surrounding multipart form is provided by the caller. --}}
<div x-data="{
        files: [],
        dragging: false,
        refresh(input) {
            this.files = Array.from(input.files).map(f => ({
                name: f.name,
                size: (f.size / 1024 / 1024).toFixed(1),
                title: f.name.replace(/\.[^.]+$/, '').replace(/[_-]+/g, ' '),
                premium: false,
            }));
        },
        drop(e) {
            this.dragging = false;
            this.$refs.input.files = e.dataTransfer.files;
            this.refresh(this.$refs.input);
        },
        remove(i) {
            const dt = new DataTransfer();
            Array.from(this.$refs.input.files).forEach((f, idx) => { if (idx !== i) dt.items.add(f); });
            this.$refs.input.files = dt.files;
            this.files.splice(i, 1);
        }
    }" class="space-y-4">
    <label @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop.prevent="drop($event)"
           :class="dragging ? 'border-black bg-gray-50' : 'border-gray-300'"
           class="flex flex-col items-center justify-center gap-2 p-8 rounded-2xl border-2 border-dashed cursor-pointer hover:border-black transition text-center">
        <x-heroicon-o-cloud-arrow-up class="w-8 h-8 text-gray-400" />
        <span class="text-sm font-semibold text-gray-800">Tarik berkas ke sini atau klik untuk memilih</span>
        <span class="text-2xs text-gray-500">PDF, DOC, DOCX, XLS, XLSX &middot; maks 15 MB per berkas &middot; hingga 20 berkas</span>
        <input type="file" name="documents[]" multiple x-ref="input" accept=".pdf,.doc,.docx,.xls,.xlsx" class="sr-only" @change="refresh($event.target)">
    </label>
    @error('documents') <p class="text-2xs text-red-600">{{ $message }}</p> @enderror
    @php
        $documentErrors = $errors->get('documents.*');
    @endphp
    @foreach($documentErrors as $messages)
        @foreach($messages as $message) <p class="text-2xs text-red-600">{{ $message }}</p> @endforeach
    @endforeach

    <div class="space-y-2" x-show="files.length" x-cloak>
        <template x-for="(file, i) in files" :key="i">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-3 rounded-xl border border-gray-200 bg-surface">
                <div class="flex-1 min-w-0">
                    <input type="text" :name="`doc_titles[${i}]`" x-model="file.title" class="w-full text-xs rounded-lg border-gray-300 focus:border-black focus:ring-black" placeholder="Judul dokumen">
                    <p class="text-2xs text-gray-400 mt-1 truncate"><span x-text="file.name"></span> &middot; <span x-text="file.size"></span> MB</p>
                </div>
                <label class="inline-flex items-center gap-1.5 text-2xs text-gray-700 cursor-pointer shrink-0">
                    <input type="checkbox" :name="`doc_premium[${i}]`" value="1" x-model="file.premium" class="w-3.5 h-3.5 rounded border-gray-300 text-black focus:ring-black">
                    Khusus Premium
                </label>
                <button type="button" @click="remove(i)" class="text-gray-400 hover:text-red-600 text-xs shrink-0" aria-label="Hapus dari daftar">&times; Hapus</button>
            </div>
        </template>
    </div>
</div>
