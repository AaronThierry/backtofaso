@if (session('status'))
    <div class="rounded-[10px] border border-ok-700/20 bg-ok-50 px-4 py-3 text-sm font-semibold text-ok-700" role="status">{{ session('status') }}</div>
@endif
@if (session('dev_code'))
    <div class="rounded-[10px] border border-gold-300 bg-gold-50 px-4 py-3 text-sm text-gold-700" role="status">
        Mode développement : votre code est <b class="tracking-widest">{{ session('dev_code') }}</b>
    </div>
@endif
