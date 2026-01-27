<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ramadhan Glow Up - Design Preview</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;600&family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (for layout utilities) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            /* Palette Colours based on Blueprint */
            --primary-sage: #8A9A5B;
            --secondary-terracotta: #E2725B;
            --bg-ivory: #FDFBF7;
            --text-charcoal: #333333;
            --highlight-gold: #E1AD01;
            
            /* Physical Specs Simulation */
            --page-width: 176mm; /* B5 Width approx */
            --page-height: 250mm; /* B5 Height approx */
            --shadow-book: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 0 20px rgba(0,0,0,0.05);
        }

        body {
            background-color: #e0e0e0; /* Table surface */
            font-family: 'Montserrat', sans-serif;
            color: var(--text-charcoal);
            margin: 0;
            padding: 40px 0;
            overflow-x: hidden;
        }

        /* Typography Override Classes */
        .font-serif-modern { font-family: 'Playfair Display', serif; }
        .font-sans-clean { font-family: 'Montserrat', sans-serif; }
        .font-handwriting { font-family: 'Dancing Script', cursive; }

        .text-sage { color: var(--primary-sage); }
        .text-terracotta { color: var(--secondary-terracotta); }
        .text-gold { color: var(--highlight-gold); }
        .bg-ivory { background-color: var(--bg-ivory); }
        .bg-sage { background-color: var(--primary-sage); }
        .bg-terracotta { background-color: var(--secondary-terracotta); }

        /* Book Structure */
        .book-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 60px;
        }

        .spread {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0; /* No gap for lay-flat look */
            max-width: 100%;
        }

        .page {
            width: var(--page-width);
            height: var(--page-height);
            background-color: var(--bg-ivory);
            box-shadow: var(--shadow-book);
            position: relative;
            padding: 30px; /* Outer margin simulation */
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            overflow: hidden; /* Crop content strictly */
        }

        /* Responsive scaling for smaller screens */
        @media (max-width: 800px) {
            .page {
                width: 95vw;
                height: auto;
                min-height: 140vw; /* Maintain rough aspect ratio */
                margin-bottom: 20px;
            }
            .spread {
                gap: 20px;
            }
        }

        /* Cover Design Specifics */
        .cover-page {
            background-color: var(--primary-sage);
            color: white;
            text-align: center;
            border-radius: 2px 8px 8px 2px; /* Slight curve on right */
            position: relative;
        }

        .cover-pattern {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            opacity: 0.1;
            background-image: radial-gradient(#fff 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .gold-foil-text {
            color: #FFD700;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
            background: linear-gradient(45deg, #FFD700, #FDB931, #FFD700);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Interior Page Specifics */
        .page-left {
            border-radius: 4px 0 0 4px;
            background: linear-gradient(90deg, #FDFBF7 95%, #f0eee9 100%); /* Shadow towards spine */
        }
        .page-right {
            border-radius: 0 4px 4px 0;
            background: linear-gradient(-90deg, #FDFBF7 95%, #f0eee9 100%); /* Shadow towards spine */
        }

        /* Decorative Elements */
        .highlight-box {
            background-color: rgba(226, 114, 91, 0.1); /* Terracotta transparent */
            border-left: 4px solid var(--secondary-terracotta);
            padding: 15px;
            border-radius: 0 8px 8px 0;
            margin: 15px 0;
        }

        .mood-booster {
            border: 1px dashed var(--highlight-gold);
            background-color: rgba(225, 173, 1, 0.05);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        /* Journaling Lines */
        .dotted-line {
            border-bottom: 1.5px dotted #d1d1d1;
            width: 100%;
            height: 30px; /* Line height */
            margin-bottom: 5px;
            font-family: 'Dancing Script', cursive;
            font-size: 1.2rem;
            color: #555;
            padding-left: 10px;
            background: transparent;
            outline: none;
        }

        /* Checkbox Custom */
        .custom-checkbox input {
            display: none;
        }
        .custom-checkbox label {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }
        .custom-checkbox span {
            width: 20px;
            height: 20px;
            border: 2px solid var(--secondary-terracotta);
            border-radius: 4px;
            margin-right: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .custom-checkbox input:checked + label span {
            background-color: var(--secondary-terracotta);
            color: white;
        }
        .custom-checkbox input:checked + label span::after {
            content: '✔';
            font-size: 12px;
        }

        /* Footer Elements */
        .page-footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #eee;
            padding-top: 15px;
            font-size: 0.8rem;
            color: #999;
        }

        .progress-bar-container {
            width: 100px;
            height: 4px;
            background-color: #eee;
            border-radius: 2px;
        }
        .progress-bar-fill {
            height: 100%;
            background-color: var(--primary-sage);
            border-radius: 2px;
        }

        /* Floral Ornament SVG Placeholder */
        .floral-ornament {
            position: absolute;
            opacity: 0.1;
            width: 150px;
            height: 150px;
            pointer-events: none;
        }
    </style>
</head>
<body>

    <div class="book-container">
        
        <!-- COVER PREVIEW -->
        <div class="spread">
            <div class="page cover-page shadow-2xl relative flex flex-col items-center justify-between py-16 px-8">
                <div class="cover-pattern"></div>
                
                <!-- Top Badge -->
                <div class="z-10 bg-white/10 backdrop-blur-sm px-4 py-1 rounded-full border border-white/20">
                    <p class="text-xs uppercase tracking-[0.2em] font-sans-clean text-white">Interactive Workbook</p>
                </div>

                <!-- Main Titles -->
                <div class="z-10 text-center w-full">
                    <p class="font-sans-clean text-sm tracking-widest mb-4 opacity-90">RAMADHAN JOURNAL FOR THE MODERN MUSLIMAH</p>
                    <h1 class="font-serif-modern text-6xl leading-tight mb-2 gold-foil-text font-bold">Ramadhan<br>Glow Up</h1>
                    <div class="h-1 w-20 bg-[#E1AD01] mx-auto my-6 rounded-full"></div>
                    <h2 class="font-serif-modern text-2xl italic tracking-wide text-white">30 Hari Menata Hati,<br>Merawat Diri</h2>
                </div>

                <!-- Illustration Placeholder -->
                <div class="z-10 my-4 opacity-80">
                   <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round" class="text-white mx-auto">
                        <path d="M12 2L12 22"></path>
                        <path d="M12 12C6 12 2 16 2 22"></path>
                        <path d="M12 12C18 12 22 16 22 22"></path>
                        <path d="M12 2C7 2 7 7 7 7"></path>
                        <path d="M12 2C17 2 17 7 17 7"></path>
                   </svg>
                   <p class="text-[10px] mt-2 font-handwriting text-xl">Wulan Setya Ningsih</p>
                </div>

                <!-- Bottom Badge -->
                <div class="z-10 self-end">
                    <div class="w-20 h-20 rounded-full border-2 border-white/30 flex items-center justify-center rotate-[-15deg] backdrop-blur-sm bg-white/5">
                        <p class="text-[8px] text-center leading-tight font-sans-clean font-bold">READ<br>WRITE<br>HEAL</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <p class="text-sm text-gray-500 font-sans-clean mb-2">▼ SPREAD CONTOH: DAY 1 ▼</p>
            <p class="text-xs text-gray-400">Layout Buka (Lay-Flat)</p>
        </div>

        <!-- INSIDE SPREAD: DAY 1 -->
        <div class="spread">
            
            <!-- LEFT PAGE (MATERI) -->
            <div class="page page-left">
                <!-- Coloring Corner -->
                <svg class="floral-ornament top-0 right-0 -mr-8 -mt-8 text-sage rotate-90" viewBox="0 0 100 100" fill="currentColor">
                   <path d="M50 0 C20 0 0 20 0 50 C0 80 20 100 50 100 C80 100 100 80 100 50 C100 20 80 0 50 0 Z M50 90 C30 90 10 70 10 50 C10 30 30 10 50 10 C70 10 90 30 90 50 C90 70 70 90 50 90 Z" opacity="0.1"/>
                </svg>

                <div class="flex items-baseline justify-between mb-6 border-b-2 border-dashed border-[#8A9A5B] pb-2">
                    <h2 class="font-serif-modern text-4xl text-sage font-bold">Day 01</h2>
                    <span class="font-sans-clean text-xs uppercase tracking-widest text-terracotta font-bold">The Foundation</span>
                </div>

                <div class="mood-booster flex gap-3 items-start">
                    <i class="fas fa-star text-gold mt-1"></i>
                    <div>
                        <p class="font-sans-clean text-sm italic text-gray-600">
                            “Sesungguhnya Allah tidak akan mengubah nasib suatu kaum hingga mereka mengubah keadaan diri mereka sendiri.”
                        </p>
                        <p class="text-xs text-right mt-1 font-bold text-sage">(QS. Ar-Ra’d: 11)</p>
                    </div>
                </div>

                <h3 class="font-serif-modern text-2xl mb-2 text-gray-800">Bismillah for A New Me</h3>
                <p class="font-sans-clean text-xs text-terracotta uppercase tracking-wide mb-4">Menata Niat & Start Awal</p>

                <div class="prose prose-sm font-sans-clean text-gray-600 leading-relaxed text-justify text-[13px]">
                    <p class="mb-3">
                        <span class="font-handwriting text-xl text-sage block mb-1">Assalamu’alaikum, Ramadhan!</span>
                        Sister, selamat datang di hari pertama. Tarik napas panjang... dan rasakan bedanya udara pagi ini. Ada ketenangan yang menyusup, kan? Itu tanda rahmat Allah sedang turun deras-derasnya.
                    </p>
                    <p class="mb-3">
                        Hari pertama biasanya semangat kita sedang on fire. Tapi hati-hati, dalam psikologi ada yang namanya <strong class="text-terracotta">"False Hope Syndrome"</strong>—terlalu bersemangat di awal, pasang target ketinggian, lalu <i>burnout</i> di tengah jalan.
                    </p>
                    <div class="highlight-box text-xs italic bg-gray-100 border-l-4 border-gray-400">
                        <p class="font-bold text-gray-700 not-italic mb-1">✨ Tips Psikologis Ibu Wulan:</p>
                        "Validasi rasanya. Bilang ke diri sendiri: 'Gapapa pusing dikit, ini tanda tubuh lagi bersih-bersih'. Istirahatlah sejenak (Qailulah), jangan dipaksa."
                    </div>
                    <p>
                        Kunci hari ini adalah: <strong>INTENTION SETTING</strong>. Kalau niat puasamu cuma "Yang penting gugur kewajiban", maka kamu akan merasa puasa ini beban. Ubah mindset: "Aku puasa karena aku butuh Engkau."
                    </p>
                </div>

                <div class="page-footer">
                    <span>Page 02</span>
                    <div class="flex flex-col items-end">
                        <span class="text-[10px] mb-1">LEVEL 1: PREPARATION</span>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" style="width: 3.3%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT PAGE (ACTION) -->
            <div class="page page-right">
                 <!-- Coloring Corner -->
                 <svg class="floral-ornament top-0 left-0 -ml-8 -mt-8 text-sage" viewBox="0 0 100 100" fill="currentColor">
                    <circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="2" fill="none"/>
                    <path d="M50 10 L50 90 M10 50 L90 50" stroke="currentColor" stroke-width="2"/>
                 </svg>

                <h3 class="font-handwriting text-3xl text-terracotta mb-6 text-center transform -rotate-2">Action Plan Hari Ini</h3>

                <div class="grid grid-cols-1 gap-6 mb-6">
                    <!-- Section Fisik -->
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                        <div class="flex items-center gap-2 mb-3 border-b pb-2">
                            <i class="fas fa-sun text-terracotta"></i>
                            <h4 class="font-sans-clean font-bold text-sm text-gray-700">Merawat Diri (Fisik)</h4>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="c1">
                            <label for="c1"><span></span>Sahur Bergizi (Walau sedikit)</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="c2">
                            <label for="c2"><span></span>Hydration (2 Gelas saat Sahur)</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="c3">
                            <label for="c3"><span></span>Lisan yang Cantik (No complaining)</label>
                        </div>
                    </div>

                    <!-- Section Hati -->
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                        <div class="flex items-center gap-2 mb-3 border-b pb-2">
                            <i class="fas fa-heart text-sage"></i>
                            <h4 class="font-sans-clean font-bold text-sm text-gray-700">Menata Hati (Spiritual)</h4>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="c4">
                            <label for="c4"><span></span>Luruskan Niat ("Nawaitu...")</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="c5">
                            <label for="c5"><span></span>Memaafkan Masa Lalu</label>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" id="c6">
                            <label for="c6"><span></span>Mindful Moment (Nikmati lapar)</label>
                        </div>
                    </div>
                </div>

                <!-- Journaling Area -->
                <div class="flex-grow">
                    <h4 class="font-serif-modern text-lg mb-2 text-gray-800">Journaling: Intention Setting</h4>
                    <p class="text-xs text-gray-500 mb-2">Apa "Why" (Alasan Kuat) aku ikut Ramadhan tahun ini?</p>
                    
                    <!-- Editable Lines -->
                    <div class="dotted-line" contenteditable="true"></div>
                    <div class="dotted-line" contenteditable="true"></div>
                    <div class="dotted-line" contenteditable="true"></div>
                    
                    <p class="text-xs text-gray-500 mt-4 mb-2">Satu kebiasaan buruk yang ingin aku "puasakan" hari ini?</p>
                    <div class="dotted-line" contenteditable="true"></div>
                    <div class="dotted-line" contenteditable="true"></div>
                </div>

                <!-- Footer Tracker -->
                <div class="page-footer bg-sage/10 -mx-8 -mb-8 px-8 py-4 mt-4 border-t-0 flex flex-col gap-2">
                    <div class="flex justify-between items-center w-full">
                        <span class="text-[10px] font-bold text-sage uppercase">Mood Check</span>
                        <div class="flex gap-2 text-lg text-gray-400">
                            <button class="hover:text-terracotta hover:scale-110 transition">😁</button>
                            <button class="hover:text-terracotta hover:scale-110 transition">😐</button>
                            <button class="hover:text-terracotta hover:scale-110 transition">😴</button>
                            <button class="hover:text-terracotta hover:scale-110 transition">😟</button>
                        </div>
                    </div>
                    <div class="flex justify-between items-center w-full">
                         <span class="text-[10px] font-bold text-sage uppercase">Water</span>
                         <div class="flex gap-1 text-blue-300 text-xs">
                             <i class="fas fa-tint"></i><i class="fas fa-tint"></i><i class="fas fa-tint"></i><i class="fas fa-tint"></i>
                             <i class="fas fa-tint opacity-30"></i><i class="fas fa-tint opacity-30"></i><i class="fas fa-tint opacity-30"></i><i class="fas fa-tint opacity-30"></i>
                         </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</body>
</html>