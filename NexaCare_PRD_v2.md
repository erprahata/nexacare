# NexaCare

**Product Requirements Document (PRD) v2.0**  
**Owner:** RHT Labs  
**Source:** `NexaCare_PRD_v2.pdf`

## Delivery Checkpoint - 21 August 2026

Status below is based on the repository implementation, not only on database migrations or visual mockups.

| Checkpoint | PRD scope | Repository evidence | Status |
| --- | --- | --- | --- |
| C1 | Laravel, Inertia, Vue, Tailwind foundation | Laravel app, Inertia pages, Vite build | Done |
| C2 | MySQL domain foundation | Clinics, users, patients, appointments, medicines, EMR, prescriptions, invoices and invoice items migrations/models | Done, pending integration hardening |
| C3 | Registration and basic queue | Frontdesk registration creates patient and appointment with initial ETA | Done, basic only |
| C4 | Doctor dashboard and EMR | Authenticated dashboard, diagnosis, notes, prescription, stock decrement, invoice creation | Done, basic only |
| C5 | Decentralized pharmacy | Pending prescription list and completion action | Partial: UI/action exists; real worker and real-time delivery do not |
| C6 | Cashier workflow | Unpaid invoice list and payment status update | Partial: manual simulation; payment gateway/e-Receipt absent |
| C7 | JIT Queueing Engine | Static `now()+15 minutes` ETA in registration | Not done |
| C8 | Ripple Effect / Emergency Override | No emergency action, temporary identity, queue recalculation, or broadcast | Not done |
| C9 | Break-Glass EMR and audit logging | No dedicated authorization/audit-log implementation | Not done |
| C10 | VVIP masking and concierge workflow | VVIP flag and badge exist | Partial: masking and concierge routing absent |
| C11 | Node.js worker, WhatsApp/email, Discord | No worker service or integrations in repository | Not done |
| C12 | UUID security posture | UUID migrations and `HasUuids` on sensitive models | Partial: inconsistent legacy/user/clinic key strategy needs review |
| C13 | Multi-tenant / white-label SaaS | No tenant boundary, tenant context, or branding configuration | Not done |
| C14 | Security QA and penetration testing | Validation and Eloquent are present; no test evidence or Burp test plan in repo | Partial |

### Project Flow Checkpoints

1. **Foundation:** complete enough for continued MVP development.
2. **MVP clinical flow:** registration -> queue -> doctor EMR -> pharmacy/cashier handoff is demonstrable, but ETA remains static and the handoffs are synchronous.
3. **Operational automation:** blocked until the Node.js worker contract, queue events, notification provider credentials, and retry/observability policy are defined.
4. **Emergency safety:** blocked until role permissions, break-glass reason capture, immutable audit events, temporary patient identity, and queue pause/recalculation rules are approved.
5. **Enterprise readiness:** blocked until tenant isolation, VVIP masking policy, deployment topology, security test evidence, and white-label configuration are implemented.

## Addendum A - Ketentuan Antarmuka Glassmorphism

Seluruh antarmuka NexaCare, termasuk halaman publik, autentikasi, dashboard, form, tabel, modal, dropdown, toast, date picker, checkbox, input, textarea, tombol, badge, empty state, dan navigasi, **wajib menggunakan tema glassmorphism** yang konsisten.

### Parameter wajib

- Gunakan permukaan semi-transparan, `backdrop-blur`, border transparan/putih tipis, shadow lembut, dan radius konsisten maksimal `rounded-3xl`.
- Komponen tidak boleh kembali ke native styling polos bila tersedia komponen glass reusable.
- Komponen reusable yang menjadi standar: `GlassSelect.vue`, `GlassToast.vue`, `GlassInput.vue`, `GlassTextarea.vue`, `GlassDatePicker.vue`, `GlassCheckbox.vue`, dan `GlassButton.vue`.
- State `hover`, `focus`, `disabled`, `loading`, `error`, `empty`, dan `selected` wajib tetap terbaca pada permukaan transparan serta tidak mengubah ukuran layout.
- Kontras teks dan indikator error wajib diuji pada viewport desktop dan mobile.
- Halaman baru wajib menggunakan komponen standar sebelum menambahkan styling inline. Pengecualian harus dicatat dalam review UI.

### Definition of Done UI

- Tidak ada form control baru yang memakai native appearance tanpa alasan aksesibilitas yang terdokumentasi.
- Komponen memiliki API `v-model` atau props/events yang konsisten dengan Vue 3.
- Build Vite lulus dan workflow utama dapat diuji pada desktop serta mobile.
- Perubahan visual tidak mengorbankan keyboard focus, label, pesan error, atau status loading.

## Release Roadmap

- **Phase 1 - MVP:** database, registration, doctor dashboard/EMR, basic queue, pharmacy and cashier handoff, and glass UI system.
- **Phase 2 - Automation:** Node.js worker, dynamic JIT ETA, Ripple Effect, real-time notifications, pharmacy and cashier integrations.
- **Phase 3 - Security and SaaS:** break-glass UGD, immutable audit logs, VVIP authorization/masking, penetration testing, tenant isolation, and white-labeling.