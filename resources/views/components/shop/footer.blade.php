<footer class="flex shrink-0 items-center justify-between bg-zinc-100 px-12 py-7">
    <flux:text size="sm">© {{ date('Y') }} KaayKongKruaOnline. Free returns on every order for 60 years.</flux:text>
    <div class="flex gap-5">
        <button type="button" onclick="openInfoModal('terms')" class="text-[13px] text-zinc-500 hover:text-zinc-800 transition-colors bg-transparent border-0 cursor-pointer p-0">Terms</button>
        <button type="button" onclick="openInfoModal('privacy')" class="text-[13px] text-zinc-500 hover:text-zinc-800 transition-colors bg-transparent border-0 cursor-pointer p-0">Privacy</button>
        <button type="button" onclick="openInfoModal('contact')" class="text-[13px] text-zinc-500 hover:text-zinc-800 transition-colors bg-transparent border-0 cursor-pointer p-0">Contact</button>
    </div>
</footer>
{{-- Floating Info Modal --}}
<div id="footerInfoModal" class="info-modal-overlay" style="display: none;" onclick="closeInfoModal()">
    <div class="info-modal-card" onclick="event.stopPropagation()">
        <div class="info-modal-header">
            <div>
                <h3 id="infoModalTitle" class="info-modal-title">—</h3>
                <p id="infoModalSubtitle" class="info-modal-subtitle">—</p>
            </div>
            <button type="button" class="info-modal-close" onclick="closeInfoModal()">&times;</button>
        </div>
        <div id="infoModalBody" class="info-modal-body">
            {{-- Content injected via JavaScript --}}
        </div>
        <div class="info-modal-footer">
            <button type="button" class="info-btn-close" onclick="closeInfoModal()">Close</button>
        </div>
    </div>
</div>
<style>
    .info-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        backdrop-filter: blur(3px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 99999;
        padding: 16px;
    }
    .info-modal-card {
        background: #ffffff;
        border-radius: 16px;
        width: 100%;
        max-width: 520px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        animation: infoFadeIn 0.15s ease-out;
    }
    @keyframes infoFadeIn {
        from { opacity: 0; transform: scale(0.96); }
        to { opacity: 1; transform: scale(1); }
    }
    .info-modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        padding: 20px 24px 16px;
        border-bottom: 1px solid #f4f4f5;
    }
    .info-modal-title { font-size: 18px; font-weight: 600; color: #18181b; margin: 0; }
    .info-modal-subtitle { font-size: 13px; color: #71717a; margin: 4px 0 0; }
    .info-modal-close {
        background: none;
        border: none;
        font-size: 22px;
        line-height: 1;
        color: #a1a1aa;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 6px;
    }
    .info-modal-close:hover { color: #18181b; background: #f4f4f5; }
    .info-modal-body {
        padding: 20px 24px;
        max-height: 65vh;
        overflow-y: auto;
        font-size: 14px;
        line-height: 1.6;
        color: #3f3f46;
    }
    .info-modal-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding: 14px 24px;
        border-top: 1px solid #f4f4f5;
        background: #fafafa;
    }
    .info-btn-close {
        height: 36px;
        padding: 0 18px;
        border-radius: 8px;
        background: #18181b;
        color: #ffffff;
        border: none;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.15s;
    }
    .info-btn-close:hover { background: #27272a; }
</style>
<script>
    const infoContents = {
        terms: {
            title: 'Terms of Service',
            subtitle: 'Last updated: November 2026',
            html: `
                <div class="flex flex-col gap-4 text-sm text-zinc-600">
                    <div>
                        <h4 class="font-semibold text-zinc-900 mb-1">Acceptance of Terms</h4>
                        <p>You don't have to accept anything we are just some random e-commerce website</p>
                    </div>
                    
                </div>
            `
        },
        privacy: {
            title: 'Privacy Policy',
            subtitle: 'How we safeguard your personal information',
            html: `
                <div class="flex flex-col gap-4 text-sm text-zinc-600">
                    <div>
                        <h4 class="font-semibold text-zinc-900 mb-1">Information We Collect</h4>
                        <p>We actually don't collect anything due to our laziness</p>
                    </div>
                </div>
            `
        },
        contact: {
            title: 'Contact Us',
            subtitle: 'XD',
            html: `
                <div class="flex flex-col gap-4 text-sm text-zinc-600">
                    <div class="rounded-xl border border-zinc-200 bg-zinc-50/50 p-4 flex flex-col gap-3">
                        <div class="flex items-start gap-3">
                            <span class="text-base">📍</span>
                            <div>
                                <p class="font-semibold text-zinc-900">Store Address</p>
                                <p class="text-xs text-zinc-500">Chiang Mai University, 30 years Engineer Building, 422</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="text-base">📞</span>
                            <div>
                                <p class="font-semibold text-zinc-900">Phone Support</p>
                                <p class="text-xs text-zinc-500">0623183115 (Mon–Sat, 09:00 – 18:00)</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="text-base">✉️</span>
                            <div>
                                <p class="font-semibold text-zinc-900">Email Inquiries</p>
                                <p class="text-xs text-zinc-500">kaaykongkrua@Zmail.com</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="text-base">⏱️</span>
                            <div>
                                <p class="font-semibold text-zinc-900">Operating Hours</p>
                                <p class="text-xs text-zinc-500">Monday – Saturday: 9:00 AM – 6:00 PM (Closed on Sundays)</p>
                            </div>
                        </div>
                    </div>
                </div>
            `
        }
    };
    function openInfoModal(type) {
        const item = infoContents[type];
        if (!item) return;
        document.getElementById('infoModalTitle').textContent = item.title;
        document.getElementById('infoModalSubtitle').textContent = item.subtitle;
        document.getElementById('infoModalBody').innerHTML = item.html;
        document.getElementById('footerInfoModal').style.display = 'flex';
    }
    function closeInfoModal() {
        document.getElementById('footerInfoModal').style.display = 'none';
    }
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeInfoModal();
    });
</script>