---
name: modern-auth-ui
description: Merancang dan membangun halaman autentikasi (Login, Register, Forgot Password) yang modern, estetis, responsif, dan memiliki UX tinggi.
---
# Modern Auth UI Skill

Saat diminta membuat atau memperbarui halaman login/autentikasi, terapkan standar desain berikut secara otomatis:

## 1. Tata Letak (Layout)
- Gunakan **Split-Screen Layout** untuk desktop (sisi kiri: form login bersih; sisi kanan: visual abstrak, ilustrasi 3D, atau *brand showcase* dengan *gradient* halus) atau **Centered Glassmorphism Card** dengan latar belakang *mesh gradient* yang elegan.
- Pastikan 100% responsif di layar *mobile* tanpa *horizontal scroll*.

## 2. Estetika & Styling
- Gunakan **Tailwind CSS** (atau CSS modern) dengan palet warna harmonis (misalnya *slate/zinc* untuk mode gelap atau *off-white* bersih untuk mode terang, dipadukan dengan satu warna aksen yang kontras).
- Tambahkan *micro-interactions*: efek *focus ring* halus pada kolom input, transisi *hover* pada tombol, serta animasi *loading spinner* saat proses submit.
- Berikan jarak (*whitespace*) yang lega antar elemen (`gap-4` atau `gap-6`) agar tidak terlihat penuh sesak.

## 3. Kelengkapan Fitur UX
- Sertakan tombol **Show/Hide Password** (ikon mata) di dalam input kata sandi.
- Tambahkan opsi **Remember Me**, tautan **Lupa Kata Sandi**, serta tombol **OAuth / Social Login** (Google, GitHub) dengan ikon SVG resmi yang rapi.
- Buat tampilan *error state* yang jelas (border merah halus + pesan validasi di bawah input) dan status *disabled* pada tombol saat sedang memuat.
