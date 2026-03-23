<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixHindiQuestionsSeeder extends Seeder
{
    /**
     * Fix Hindi translations for ALL quiz questions.
     * Maps by question ID to ensure exact 1:1 match with English.
     */
    public function run(): void
    {
        // ═══════════════════════════════════════════════
        // OCEAN Assessment (IDs 61–90)
        // ═══════════════════════════════════════════════

        $oceanTranslations = [
            // ── Neuroticism (domain 5) ──
            61 => ['title_hi' => 'मैं चीज़ों की चिंता करता/करती हूँ।'],                          // Worry about things.
            62 => ['title_hi' => 'मैं जल्दी गुस्सा हो जाता/जाती हूँ।'],                          // Get angry easily.
            63 => ['title_hi' => 'मैं अक्सर उदास महसूस करता/करती हूँ।'],                         // Often feel blue.
            64 => ['title_hi' => 'मैं अपनी ओर ध्यान आकर्षित करने से डरता/डरती हूँ।'],           // Am afraid to draw attention to myself.
            65 => ['title_hi' => 'मैं अत्यधिक खाने-पीने में लग जाता/जाती हूँ।'],                  // Go on binges.
            66 => ['title_hi' => 'मैं आसानी से तनावग्रस्त हो जाता/जाती हूँ।'],                    // Get stressed out easily.

            // ── Extraversion (domain 3) ──
            67 => ['title_hi' => 'मैं आसानी से दोस्त बना लेता/लेती हूँ।'],                        // Make friends easily.
            68 => ['title_hi' => 'मुझे बड़ी पार्टियाँ पसंद हैं।'],                                  // Love large parties.
            69 => ['title_hi' => 'मैं नेतृत्व करता/करती हूँ।'],                                     // Take charge.
            70 => ['title_hi' => 'मैं हमेशा चलता-फिरता रहता/रहती हूँ।'],                          // Am always on the go.
            71 => ['title_hi' => 'मुझे उत्साह पसंद है।'],                                          // Love excitement.
            72 => ['title_hi' => 'मैं खुशी बिखेरता/बिखेरती हूँ।'],                                 // Radiate joy.

            // ── Openness (domain 1) ──
            73 => ['title_hi' => 'मेरी कल्पना शक्ति बहुत तेज़ है।'],                               // Have a vivid imagination.
            74 => ['title_hi' => 'मैं कला के महत्व में विश्वास करता/करती हूँ।'],                   // Believe in the importance of art.
            75 => ['title_hi' => 'मैं अपनी भावनाओं को तीव्रता से अनुभव करता/करती हूँ।'],          // Experience my emotions intensely.
            76 => ['title_hi' => 'मैं दिनचर्या की बजाय विविधता पसंद करता/करती हूँ।'],             // Prefer variety to routine.
            77 => ['title_hi' => 'मुझे अमूर्त विचारों को समझने में कठिनाई होती है।'],              // Have difficulty understanding abstract ideas.
            78 => ['title_hi' => 'मैं उदारवादी (प्रगतिशील) राजनीतिक उम्मीदवारों को वोट देता/देती हूँ।'], // Tend to vote for liberal political candidates.

            // ── Agreeableness (domain 4) ──
            79 => ['title_hi' => 'मैं दूसरों पर भरोसा करता/करती हूँ।'],                            // Trust others.
            80 => ['title_hi' => 'मैं सच बोलता/बोलती हूँ।'],                                      // Tell the truth.
            81 => ['title_hi' => 'मुझे दूसरों की मदद करना पसंद है।'],                              // Love to help others.
            82 => ['title_hi' => 'मुझे बेघर लोगों से सहानुभूति होती है।'],                          // Sympathise with the homeless.
            83 => ['title_hi' => 'मैं मानता/मानती हूँ कि मैं दूसरों से बेहतर हूँ।'],               // Believe that I am better than others.
            84 => ['title_hi' => 'मुझे उन लोगों से सहानुभूति होती है जो मुझसे बदतर स्थिति में हैं।'], // Feel sympathy for those who are worse off.

            // ── Conscientiousness (domain 2) ──
            85 => ['title_hi' => 'मैं कार्यों को सफलतापूर्वक पूरा करता/करती हूँ।'],                // Complete tasks successfully.
            86 => ['title_hi' => 'मुझे साफ-सफाई करना पसंद है।'],                                  // Like to tidy up.
            87 => ['title_hi' => 'मैं अपने वादे निभाता/निभाती हूँ।'],                              // Keep my promises.
            88 => ['title_hi' => 'मैं कड़ी मेहनत करता/करती हूँ।'],                                  // Work hard.
            89 => ['title_hi' => 'मैं अपनी इच्छाओं पर नियंत्रण रख सकता/सकती हूँ।'],              // Am able to control my cravings.
            90 => ['title_hi' => 'मैं जल्दबाज़ी में फैसले लेता/लेती हूँ।'],                         // Make rash decisions.
        ];

        // ═══════════════════════════════════════════════
        // RIASEC Assessment (IDs 91–108)
        // ═══════════════════════════════════════════════

        $riasecTranslations = [
            // ── Realistic (domain 6) ──
            91 => ['title_hi' => 'रसोई की अलमारियाँ बनाना'],                                      // Build kitchen cabinets
            92 => ['title_hi' => 'घरेलू उपकरणों की मरम्मत करना'],                                 // Repair household appliances
            93 => ['title_hi' => 'जंगल की आग बुझाना'],                                             // Put out forest fires

            // ── Investigative (domain 7) ──
            94 => ['title_hi' => 'एक नई दवा विकसित करना'],                                        // Develop a new medicine
            95 => ['title_hi' => 'रासायनिक प्रयोग करना'],                                          // Conduct chemical experiments
            96 => ['title_hi' => 'बीमारियों की पहचान के लिए प्रयोगशाला परीक्षण करना'],            // Do laboratory tests to identify diseases

            // ── Artistic (domain 8) ──
            97 => ['title_hi' => 'किताबें या नाटक लिखना'],                                          // Write books or plays
            98 => ['title_hi' => 'संगीत वाद्ययंत्र बजाना'],                                        // Play a musical instrument
            99 => ['title_hi' => 'फिल्मों के लिए विशेष प्रभाव बनाना'],                             // Create special effects for movies

            // ── Social (domain 9) ──
            100 => ['title_hi' => 'व्यक्तिगत या भावनात्मक समस्याओं में लोगों की मदद करना'],        // Help people with personal or emotional problems
            101 => ['title_hi' => 'बच्चों को खेल खेलना सिखाना'],                                    // Teach children how to play sports
            102 => ['title_hi' => 'डे-केयर सेंटर में बच्चों की देखभाल करना'],                       // Take care of children at a day-care center

            // ── Enterprising (domain 10) ──
            103 => ['title_hi' => 'अपना खुद का व्यवसाय शुरू करना'],                                // Start your own business
            104 => ['title_hi' => 'व्यावसायिक अनुबंधों पर बातचीत करना'],                            // Negotiate business contracts
            105 => ['title_hi' => 'कपड़ों की एक नई लाइन का विपणन करना'],                           // Market a new line of clothing

            // ── Conventional (domain 11) ──
            106 => ['title_hi' => 'कम्प्यूटर सॉफ्टवेयर का उपयोग करके स्प्रेडशीट बनाना'],         // Develop a spreadsheet using computer software
            107 => ['title_hi' => 'कर्मचारियों के वेतन की गणना करना'],                              // Calculate the wages of employees
            108 => ['title_hi' => 'किसी संगठन के लिए डाक पर मुहर लगाना, छाँटना और वितरित करना'],  // Stamp, sort, and distribute mail
        ];

        // ═══════════════════════════════════════════════
        // Cognitive Assessment (IDs 109–120)
        // ═══════════════════════════════════════════════

        $cognitiveTranslations = [
            // ── Verbal Ability (domain 12) ──
            109 => [
                'title_hi' => '\'Happy\' (खुश) के सबसे समान अर्थ वाला शब्द चुनें',
                'options_hi' => json_encode(["उदास" => 0, "प्रसन्न" => 1, "क्रोधित" => 0, "कमज़ोर" => 0]),
            ], // Choose the word most similar to 'Happy'
            110 => [
                'title_hi' => 'कौन सा शब्द इस समूह में नहीं है?',
                'options_hi' => json_encode(["सेब" => 0, "केला" => 0, "गाजर" => 1, "आम" => 0]),
            ], // Which word does not belong
            111 => [
                'title_hi' => 'सादृश्य पूरा करें: हाथ का दस्ताने से वही संबंध है जो पैर का ___ से है',
                'options_hi' => json_encode(["टोपी" => 0, "मोज़ा" => 1, "जूता" => 0, "कोट" => 0]),
            ], // Hand is to Glove as Foot is to ___

            // ── Numerical Ability (domain 13) ──
            112 => [
                'title_hi' => '200 का 15% क्या है?',
                'options_hi' => json_encode(["20" => 0, "25" => 0, "30" => 1, "35" => 0]),
            ], // What is 15% of 200?
            113 => [
                'title_hi' => 'अगली संख्या क्या आएगी: 2, 4, 8, 16, ?',
                'options_hi' => json_encode(["20" => 0, "24" => 0, "30" => 0, "32" => 1]),
            ], // What number comes next: 2, 4, 8, 16, ?
            114 => [
                'title_hi' => 'एक ट्रेन 1.5 घंटे में 60 किमी चलती है। उसकी औसत गति क्या है?',
                'options_hi' => json_encode(["30 किमी/घंटा" => 0, "40 किमी/घंटा" => 0, "45 किमी/घंटा" => 1, "50 किमी/घंटा" => 0]),
            ], // A train travels 60 km in 1.5 hours

            // ── Logical Reasoning (domain 14) ──
            115 => [
                'title_hi' => 'कौन सा जोड़ा सबसे समान है: (A) बिल्ली–कुत्ता (B) कलम–कागज़ (C) कुर्सी–मेज़ (D) सूरज–चाँद',
                'options_hi' => json_encode(["A" => 0, "B" => 1, "C" => 0, "D" => 0]),
            ], // Which pair is most similar
            116 => [
                'title_hi' => 'विषम संख्या ज्ञात करें: 3, 7, 11, 14, 19',
                'options_hi' => json_encode(["3" => 0, "7" => 0, "11" => 0, "14" => 1]),
            ], // Find the odd one out
            117 => [
                'title_hi' => 'श्रृंखला पूरी करें: 5, 10, 20, 40, ?',
                'options_hi' => json_encode(["50" => 0, "60" => 0, "70" => 0, "80" => 1]),
            ], // Complete the series: 5, 10, 20, 40, ?

            // ── Working Memory (domain 15) ──
            118 => [
                'title_hi' => 'इस क्रम को याद करें और उलटा लिखें: 3–8–1–6',
                'options_hi' => json_encode(["1683" => 0, "6813" => 0, "6138" => 0, "6183" => 1]),
            ], // Remember this sequence and type it backwards: 3–8–1–6
            119 => [
                'title_hi' => 'आपने संख्याएँ सुनीं 7–2–9–5। दूसरी संख्या क्या थी?',
                'options_hi' => json_encode(["2" => 1, "5" => 0, "7" => 0, "9" => 0]),
            ], // You hear the numbers 7–2–9–5. What was the second number?
            120 => [
                'title_hi' => 'अक्षरों को क्रम में याद करें: K–M–P–R',
                'options_hi' => json_encode(["K–M–P–R" => 1, "K–R–M–P" => 0, "P–K–M–R" => 0, "M–K–R–P" => 0]),
            ], // Recall the letters in order: K–M–P–R
        ];

        // ── Likert scale options in Hindi (for OCEAN & RIASEC) ──
        $likertOptionsHi = json_encode([
            'बिल्कुल सहमत नहीं',
            'सहमत नहीं',
            'तटस्थ',
            'सहमत',
            'पूरी तरह सहमत',
        ]);

        // ═══════════════════════════════════════════════
        // Apply OCEAN translations
        // ═══════════════════════════════════════════════
        foreach ($oceanTranslations as $id => $data) {
            DB::table('quiz_domain_value_questions')
                ->where('id', $id)
                ->update([
                    'title_hi'   => $data['title_hi'],
                    'options_hi' => $likertOptionsHi,
                ]);
        }
        $this->command->info('✓ OCEAN: 30 questions updated (IDs 61–90)');

        // ═══════════════════════════════════════════════
        // Apply RIASEC translations
        // ═══════════════════════════════════════════════
        foreach ($riasecTranslations as $id => $data) {
            DB::table('quiz_domain_value_questions')
                ->where('id', $id)
                ->update([
                    'title_hi'   => $data['title_hi'],
                    'options_hi' => $likertOptionsHi,
                ]);
        }
        $this->command->info('✓ RIASEC: 18 questions updated (IDs 91–108)');

        // ═══════════════════════════════════════════════
        // Apply Cognitive translations (with translated options)
        // ═══════════════════════════════════════════════
        foreach ($cognitiveTranslations as $id => $data) {
            $updateData = ['title_hi' => $data['title_hi']];
            if (isset($data['options_hi'])) {
                $updateData['options_hi'] = $data['options_hi'];
            }
            DB::table('quiz_domain_value_questions')
                ->where('id', $id)
                ->update($updateData);
        }
        $this->command->info('✓ Cognitive: 12 questions updated (IDs 109–120)');

        $this->command->info('');
        $this->command->info('All 60 questions now have correct Hindi translations matched by ID.');
    }
}
