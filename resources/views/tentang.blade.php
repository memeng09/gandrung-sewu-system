<x-publik title="Tentang">
<style>
    .sejarah-layout {
        display: grid;
        grid-template-columns: 0.8fr 1.2fr;
        align-items: center;
        gap: 36px;
    }

    .sejarah-layout img {
        display: block;
        width: 100%;
        height: 280px;
        object-fit: cover;
    }

    .pemisah-tentang {
        width: 100%;
        height: 1px;
        background-color: #E5D5C5;
        margin-top: 72px;
        margin-bottom: 64px;
    }

    .judul-maestro {
        text-align: center;
        margin-bottom: 48px;
    }

    .judul-maestro h2 {
        margin-top: 10px;
    }

    .kutipan-sejarah {
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
    }

    .foto-maestro {
        height: 240px;
        width: 100%;
        overflow: hidden;
        border-radius: 8px;
        background-color: #f5f5f4;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px;
        box-sizing: border-box;
    }

    .foto-maestro img {
        max-height: 100%;
        max-width: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
    }

    @media (max-width: 768px) {
        .sejarah-layout {
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .sejarah-layout img {
            height: 230px;
        }

        .pemisah-tentang {
            margin-top: 48px;
            margin-bottom: 40px;
        }
    }
</style>

<section class="bg-[#FFFBF7] py-14 sm:py-16" x-data="{ activeMaestro: null }">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

        {{-- 1. TENTANG FESTIVAL --}}
        <div class="text-center" style="margin-bottom:64px;">
            <p class="text-sm tracking-[0.25em] text-[#C9962B] font-semibold">TENTANG FESTIVAL</p>
            <h1 class="judul text-3xl font-bold text-[#35251F] sm:text-4xl" style="margin-top:10px;">Apa itu Gandrung Sewu?</h1>
            <p class="mx-auto text-[#6B5A50]" style="margin-top:16px; max-width:672px;">
                Gandrung Sewu adalah festival tari tradisional yang menampilkan ribuan penari membawakan
                Tari Gandrung secara bersama-sama di Pantai Boom Marina, Banyuwangi. Festival ini digelar
                untuk melestarikan budaya Banyuwangi dan memperkenalkannya kepada generasi muda.
            </p>
        </div>

        {{-- 2. SEJARAH GANDRUNG --}}
        <div class="sejarah-layout">

            {{-- GAMBAR KIRI --}}
            <div>
                <img
                    src="{{ asset('images/gandrung-sejarah.jpg') }}"
                    alt="Penari Gandrung Banyuwangi"
                >
                <p class="mt-2 text-xs text-gray-500">
                    Tari Gandrung — Warisan Budaya Banyuwangi
                </p>
            </div>

            {{-- TULISAN KANAN --}}
            <div>
                <p class="mb-2 text-2xl font-bold tracking-wide text-[#8B1E2D] sm:text-3xl">
                    SEJARAH GANDRUNG
                </p>

                <h2 class="judul mb-4 text-lg font-semibold leading-snug text-[#35251F] sm:text-xl">
                    Dari Ungkapan Syukur Panen Menjadi Ikon Budaya Banyuwangi
                </h2>

                <div class="space-y-3 text-sm leading-relaxed text-gray-600 sm:text-base">
                    <p>
                        Tari Gandrung merupakan salah satu kesenian tradisional khas
                        Banyuwangi yang tumbuh dalam kehidupan masyarakat Suku Osing.
                        Kesenian ini menjadi bagian penting dari identitas budaya
                        Banyuwangi dan dahulu juga berkaitan dengan ungkapan rasa
                        syukur masyarakat atas hasil panen.
                    </p>

                    <p>
                        Dalam perkembangannya, Gandrung awalnya ditarikan oleh laki-laki
                        dengan mengenakan busana perempuan. Menjelang akhir abad ke-19,
                        tradisi ini mulai berkembang dengan penari perempuan hingga
                        menjadi bentuk Gandrung yang dikenal masyarakat saat ini.
                    </p>

                    <p>
                        Seiring waktu, Tari Gandrung terus dilestarikan dan diperkenalkan
                        kepada generasi muda melalui berbagai pertunjukan budaya.
                        Salah satunya melalui Festival Gandrung Sewu yang menjadi
                        wujud kecintaan masyarakat terhadap budaya Banyuwangi.
                    </p>
                </div>

                <div class="kutipan-sejarah">
                    <p class="judul text-sm italic text-[#8B1E2D]">
                        Menjaga warisan, merawat identitas budaya.
                    </p>
                </div>
            </div>

        </div>

        {{-- GARIS PEMISAH --}}
        <div class="pemisah-tentang"></div>

        {{-- 3. TOKOH MAESTRO --}}
        <div class="judul-maestro">
            <p class="text-sm tracking-[0.25em] text-[#C9962B] font-semibold">TENTANG FESTIVAL & TRADISI</p>
            <h2 class="judul text-3xl font-bold text-[#35251F] sm:text-4xl">Tokoh Maestro Legendaris</h2>
        </div>

        <div>
            <div class="grid gap-8 sm:grid-cols-3 items-stretch">

                {{-- Maestro 1: Mak Semi --}}
                <div @click="activeMaestro = {
                    name: 'Mak Semakin (Semi)', 
                    role: 'Pelopor Penari Perempuan Pertama (Abad ke-19)', 
                    desc: 'Lahir sekitar tahun 1885 di Desa Cungking. Berdasarkan catatan sejarah arsip kolonial, Mak Semi adalah penari Gandrung perempuan pertama yang tercatat. Konon ia mulai menari setelah sembuh dari sakit parah berkat nazarnya, mengubah tradisi yang sebelumnya hanya dibawakan oleh kaum pria.'
                }" class="box bg-white p-5 border border-[#E5D5C5] rounded-xl cursor-pointer hover:shadow-md transition flex flex-col justify-between group">
                    <div>
                        <div class="foto-maestro">
                            <img src="{{ asset('images/maestro-semi.png') }}" alt="Mak Semakin" class="drop-shadow-sm group-hover:scale-105 transition duration-500">
                        </div>
                    </div>
                    <div class="mt-5 text-center">
                        <h4 class="font-bold text-[#8B1E2D]">Mak Semakin (Semi)</h4>
                        <p class="text-xs text-[#6B5A50] mt-1">Pelopor Penari Pertama</p>
                        <span class="inline-block mt-3 text-xs font-semibold text-[#C9962B] group-hover:underline">Ketuk untuk lihat detail →</span>
                    </div>
                </div>

                {{-- Maestro 2: Temu Misti --}}
                <div @click="activeMaestro = {
                    name: 'Temu Misti (Mbok Temu)', 
                    role: 'Maestro & Pewaris Pakem Asli Gandrung', 
                    desc: 'Lahir di Desa Kemiren, Banyuwangi, pada 20 April 1953. Mbok Temu adalah ikon hidup kesenian Gandrung yang mendedikasikan hidupnya menjaga keaslian gending dan busana tradisional Suku Osing lintas generasi, serta peraih penghargaan kebudayaan nasional.'
                }" class="box bg-white p-5 border border-[#E5D5C5] rounded-xl cursor-pointer hover:shadow-md transition flex flex-col justify-between group">
                    <div>
                        <div class="foto-maestro">
                            <img src="{{ asset('images/maestro-temu.png') }}" alt="Temu Misti" class="drop-shadow-sm group-hover:scale-105 transition duration-500">
                        </div>
                    </div>
                    <div class="mt-5 text-center">
                        <h4 class="font-bold text-[#8B1E2D]">Temu Misti (Mbok Temu)</h4>
                        <p class="text-xs text-[#6B5A50] mt-1">Maestro Legendaris</p>
                        <span class="inline-block mt-3 text-xs font-semibold text-[#C9962B] group-hover:underline">Ketuk untuk lihat detail →</span>
                    </div>
                </div>

                {{-- Maestro 3: Sumitro Hadi --}}
                <div @click="activeMaestro = {
                    name: 'Sumitro Hadi', 
                    role: 'Pencipta Tari Jejer Gandrung & Koreografer', 
                    desc: 'Lahir di Desa Gladag, Rogojampi, 16 Agustus 1951. Beliau adalah maestro seniman yang menciptakan karya masterpiece Tari Jejer Gandrung (1974) yang kini menjadi fondasi koreografi penting dalam pementasan Festival Gandrung Sewu.'
                }" class="box bg-white p-5 border border-[#E5D5C5] rounded-xl cursor-pointer hover:shadow-md transition flex flex-col justify-between group">
                    <div>
                        <div class="foto-maestro">
                            <img src="{{ asset('images/maestro-sumitro.png') }}" alt="Sumitro Hadi" class="drop-shadow-sm group-hover:scale-105 transition duration-500">
                        </div>
                    </div>
                    <div class="mt-5 text-center">
                        <h4 class="font-bold text-[#8B1E2D]">Sumitro Hadi</h4>
                        <p class="text-xs text-[#6B5A50] mt-1">Pencipta Koreografi Tradisi</p>
                        <span class="inline-block mt-3 text-xs font-semibold text-[#C9962B] group-hover:underline">Ketuk untuk lihat detail →</span>
                    </div>
                </div>

            </div>
        </div>

        {{-- MODAL / POPUP --}}
        <div x-show="activeMaestro !== null" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
            <div @click.away="activeMaestro = null" class="bg-[#6E1427] w-full max-w-md rounded-2xl p-6 shadow-2xl border border-[#C9962B]/40 relative text-white">

                <p class="text-xs uppercase tracking-widest text-[#E8C468] font-semibold">Profil Tokoh Maestro</p>
                <h3 class="judul text-xl font-bold text-white mt-1" x-text="activeMaestro?.name"></h3>
                <p class="text-xs font-medium text-[#F6E7D4]/80 mt-0.5" x-text="activeMaestro?.role"></p>

                <div class="mt-3 pt-3 border-t border-[#C9962B]/30 text-xs sm:text-sm leading-relaxed text-[#F6E7D4]" x-text="activeMaestro?.desc"></div>

                <div class="mt-5 text-right">
                    <button @click="activeMaestro = null" class="px-4 py-1.5 bg-[#C9962B] text-[#6E1427] font-bold text-xs rounded-lg hover:bg-[#E8C468] transition shadow-md">Tutup</button>
                </div>
            </div>
        </div>

    </div>
</section>
</x-publik>