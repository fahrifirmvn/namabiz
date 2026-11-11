<div class="nb-card p-5 sm:p-6 h-full flex flex-col justify-between animate-pulse" aria-hidden="true">
    <div>
        <!-- Header Skeleton -->
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-start gap-3.5 min-w-0 flex-1">
                <div class="w-12 h-12 rounded-xl bg-gray-200 shrink-0"></div>
                <div class="space-y-2 flex-1 pt-0.5">
                    <div class="h-5 bg-gray-200 rounded-md w-3/4"></div>
                    <div class="h-3.5 bg-gray-100 rounded-md w-1/2"></div>
                </div>
            </div>
            <div class="w-9 h-9 rounded-lg bg-gray-100 shrink-0"></div>
        </div>

        <!-- Chips Skeleton -->
        <div class="flex items-center gap-1.5 mt-4 min-h-[24px]">
            <div class="h-6 w-20 bg-gray-100 rounded-lg"></div>
            <div class="h-6 w-24 bg-gray-100 rounded-lg"></div>
            <div class="h-6 w-16 bg-gray-100 rounded-lg"></div>
        </div>

        <!-- 2x2 Metrics Skeleton -->
        <div class="grid grid-cols-2 gap-3 bg-surface rounded-xl p-3.5 mt-4 border border-border-subtle">
            @for($i = 0; $i < 4; $i++)
                <div class="space-y-1.5">
                    <div class="h-3 bg-gray-200 rounded w-14"></div>
                    <div class="h-4 bg-gray-200 rounded w-24"></div>
                </div>
            @endfor
        </div>

        <!-- Trust Line Skeleton -->
        <div class="mt-4 flex items-center justify-between">
            <div class="h-3.5 bg-gray-100 rounded w-32"></div>
            <div class="h-3.5 bg-gray-100 rounded w-24"></div>
        </div>
    </div>

    <!-- Footer Actions Skeleton -->
    <div class="mt-5 pt-4 border-t border-border-subtle space-y-2.5">
        <div class="grid grid-cols-2 gap-2.5">
            <div class="h-10 bg-gray-200 rounded-xl"></div>
            <div class="h-10 bg-gray-100 rounded-xl"></div>
        </div>
        <div class="h-10 bg-gray-100 rounded-xl"></div>
    </div>
</div>
