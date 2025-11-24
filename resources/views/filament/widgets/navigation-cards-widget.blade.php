<x-filament-widgets::widget>
    <style>
        :root {
            --bg-color: white;
            --border-color: #e5e7eb;
            --text-color: #111827;
            --text-muted: #6b7280;
            --badge-bg: #f3f4f6;
            --badge-text: #374151;
        }
        .dark {
            --bg-color: #1f2937;
            --border-color: #374151;
            --text-color: white;
            --text-muted: #9ca3af;
            --badge-bg: #374151;
            --badge-text: #d1d5db;
        }
    </style>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
        @foreach ($this->getNavigationCards() as $card)
            <a href="{{ $card['url'] }}"
               style="display: block;
                      padding: 24px;
                      background: var(--bg-color, white);
                      border-radius: 12px;
                      border: 2px solid var(--border-color, #e5e7eb);
                      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                      text-decoration: none;
                      transition: all 0.2s;"
               onmouseover="this.style.borderColor='#1094AB'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)';"
               onmouseout="this.style.borderColor='var(--border-color, #e5e7eb)'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)';">

                {{-- Header --}}
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                    {{-- Icon container --}}
                    <div style="flex-shrink: 0; padding: 10px; border-radius: 8px; background-color: {{ $card['color'] === 'primary' ? 'rgba(16, 148, 171, 0.1)' : ($card['color'] === 'success' ? 'rgba(16, 185, 129, 0.1)' : ($card['color'] === 'warning' ? 'rgba(252, 180, 33, 0.1)' : ($card['color'] === 'gray' ? 'rgba(107, 114, 128, 0.1)' : ($card['color'] === 'info' ? 'rgba(59, 130, 246, 0.1)' : ($card['color'] === 'blue' ? 'rgba(59, 130, 246, 0.1)' : ($card['color'] === 'amber' ? 'rgba(245, 158, 11, 0.1)' : ($card['color'] === 'green' ? 'rgba(34, 197, 94, 0.1)' : ($card['color'] === 'rose' ? 'rgba(244, 63, 94, 0.1)' : ($card['color'] === 'purple' ? 'rgba(147, 51, 234, 0.1)' : 'rgba(59, 130, 246, 0.1)'))))))))) }};">
                        <svg style="width: 20px; height: 20px; color: {{ $card['color'] === 'primary' ? '#1094AB' : ($card['color'] === 'success' ? '#10b981' : ($card['color'] === 'warning' ? '#FCB421' : ($card['color'] === 'gray' ? '#6b7280' : ($card['color'] === 'info' ? '#3b82f6' : ($card['color'] === 'blue' ? '#3b82f6' : ($card['color'] === 'amber' ? '#f59e0b' : ($card['color'] === 'green' ? '#22c55e' : ($card['color'] === 'rose' ? '#f43f5e' : ($card['color'] === 'purple' ? '#9333ea' : '#3b82f6'))))))))) }};"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24"
                             stroke-width="2">
                            @if($card['icon'] === 'heroicon-o-users')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            @elseif($card['icon'] === 'heroicon-o-shield-check')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            @elseif($card['icon'] === 'heroicon-o-key')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            @elseif($card['icon'] === 'heroicon-o-document-text')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            @elseif($card['icon'] === 'heroicon-o-clipboard-document-list')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                            @elseif($card['icon'] === 'heroicon-o-star')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            @elseif($card['icon'] === 'heroicon-o-shopping-bag')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            @elseif($card['icon'] === 'heroicon-o-envelope')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            @elseif($card['icon'] === 'heroicon-o-user-group')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
                            @elseif($card['icon'] === 'heroicon-o-receipt-refund')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                            @endif
                        </svg>
                    </div>

                    {{-- Badge --}}
                    <div style="flex-shrink: 0; padding: 6px 12px; font-size: 12px; font-weight: bold; border-radius: 999px; background-color: var(--badge-bg, #f3f4f6); color: var(--badge-text, #374151);">
                        {{ $card['stats'] }}
                    </div>
                </div>

                {{-- Title --}}
                <h3 style="font-size: 18px; font-weight: bold; color: var(--text-color, #111827); margin-bottom: 8px;">
                    {{ $card['title'] }}
                </h3>

                {{-- Description --}}
                <p style="font-size: 14px; color: var(--text-muted, #6b7280); margin-bottom: 16px; line-height: 1.5;">
                    {{ $card['description'] }}
                </p>

                {{-- Footer --}}
                <div style="display: flex; align-items: center; font-size: 13px; font-weight: 600; color: {{ $card['color'] === 'primary' ? '#1094AB' : ($card['color'] === 'success' ? '#10b981' : ($card['color'] === 'warning' ? '#FCB421' : ($card['color'] === 'gray' ? '#6b7280' : ($card['color'] === 'info' ? '#3b82f6' : ($card['color'] === 'blue' ? '#3b82f6' : ($card['color'] === 'amber' ? '#f59e0b' : ($card['color'] === 'green' ? '#22c55e' : ($card['color'] === 'rose' ? '#f43f5e' : ($card['color'] === 'purple' ? '#9333ea' : '#3b82f6'))))))))) }};">
                    <span>Acessar</span>
                    <svg style="width: 14px; height: 14px; margin-left: 6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </a>
        @endforeach
    </div>
</x-filament-widgets::widget>
