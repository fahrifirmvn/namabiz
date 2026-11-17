@if($paginator->hasPages())
    <nav aria-label="Halaman hasil pemasok" class="space-y-4">
        <p class="text-center text-sm text-text-muted">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</p>
        <div class="grid grid-cols-2 gap-3">
            <button type="button" class="nb-secondary" wire:click="previousPage" wire:loading.attr="disabled"
                    @disabled($paginator->onFirstPage()) @click="document.getElementById('supplier-results').scrollIntoView({ block: 'start' })">Sebelumnya</button>
            <button type="button" class="nb-primary" wire:click="nextPage" wire:loading.attr="disabled"
                    @disabled(!$paginator->hasMorePages()) @click="document.getElementById('supplier-results').scrollIntoView({ block: 'start' })">Berikutnya</button>
        </div>
    </nav>
@endif
