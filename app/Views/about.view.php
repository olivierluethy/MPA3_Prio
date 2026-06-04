<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/favicon.ico">

    <script defer src="public/js/routes.js"></script>
    <script defer src="public/js/responsive.js"></script>
    <script defer src="public/js/aboutParallax.js"></script>

    <link rel="stylesheet" href="public/fontawesome/css/all.css">
    <link rel="stylesheet" href="public/css/app.css">
    <title>Prio — Prioritise. Track. Achieve.</title>
</head>

<body>
    <!-- Navigation Bar -->
    <?php
    $actual_link = basename(__FILE__);
    include("header.php");
    ?>

    <!-- ===================== HERO ===================== -->
    <section class="parallax-image relative flex min-h-[88vh] items-center justify-center overflow-hidden bg-surface-950">
        <!-- gradient base -->
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-surface-900 via-surface-950 to-surface-900"></div>
        <!-- subtle grid glow -->
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top,rgba(79,70,229,0.18),transparent_55%)]"></div>
        <!-- floating logo layer -->
        <img src="images/logo-parallax.png" alt="" data-parallax="0.08"
             class="pointer-events-none absolute inset-0 h-full w-full object-contain opacity-[0.06]">
        <!-- glow orbs (independent parallax depths) -->
        <div data-parallax="0.18" class="pointer-events-none absolute -top-24 left-[15%] h-72 w-72 rounded-full bg-brand-600/30 blur-3xl"></div>
        <div data-parallax="0.28" class="pointer-events-none absolute top-1/3 right-[12%] h-80 w-80 rounded-full bg-indigo-500/20 blur-3xl"></div>

        <!-- hero content -->
        <div class="relative z-10 mx-auto max-w-3xl px-4 text-center">
            <span data-reveal class="badge border border-solid border-brand-500/40 bg-brand-500/10 text-brand-200">
                <?= icon('sparkles', 'mr-1.5 h-4 w-4') ?> Productivity, done right
            </span>
            <h1 data-reveal data-reveal-delay="1" class="mt-6 text-4xl font-extrabold leading-tight tracking-tight text-white sm:text-6xl">
                Turn intentions into
                <span class="bg-gradient-to-r from-brand-400 to-indigo-300 bg-clip-text text-transparent">achievements</span>
            </h1>
            <p data-reveal data-reveal-delay="2" class="mx-auto mt-6 max-w-xl text-lg text-surface-300">
                Prio helps you prioritise <strong class="text-white">efficiently</strong> and
                <strong class="text-white">accurately</strong>, track the time you invest, and actually
                follow through on the goals you set. That’s our promise.
            </p>
            <div data-reveal data-reveal-delay="3" class="mt-9 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <button type="button" onclick="goToLogin()" class="btn-primary px-6 py-3 text-base">Get started</button>
                <a href="#features" class="btn-secondary px-6 py-3 text-base">Explore features</a>
            </div>
        </div>

        <!-- scroll cue -->
        <a href="#features" aria-label="Scroll to features"
           class="absolute bottom-6 left-1/2 -translate-x-1/2 text-surface-400 transition-colors hover:text-white">
            <?= icon('chevron-down', 'h-7 w-7 animate-bounce') ?>
        </a>
    </section>

    <!-- ===================== FEATURES ===================== -->
    <section id="features" class="mx-auto max-w-6xl px-4 py-24">
        <div data-reveal class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold text-white sm:text-4xl">Everything you need to follow through</h2>
            <p class="mt-4 text-surface-300">
                Many people set goals but never define them well enough to achieve them. Prio fixes that —
                from meaningful goal-setting to intuitive progress tracking and full data privacy.
            </p>
        </div>

        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php
            $features = [
                ['flag',     'Set Meaningful Goals',      'Define a title, describe your objective, and capture the motivation behind it — why it matters and how hard you want to push.'],
                ['chart',    'Track Progress Intuitively', 'Forget constantly checking the clock. Start the timer with a click and see a clear history of everything you’ve done, summed up for you.'],
                ['bolt',     'Achieve Goals Quickly',     'Achieving goals takes dedication. Prio tracks the time spent on each task so you can monitor real progress, not just intentions.'],
                ['calendar', 'Deadlines That Matter',     'Because productivity is the point, deadlines count. Notice how often you finish on time — and what happens when you don’t.'],
                ['shield',   'Fully Encrypted',           'All your data is encrypted at rest. A separate security layer means even we can’t read your user data in clear text.'],
                ['sparkles', 'Built for Focus',           'Prio is centred on prioritisation, so the most important work always rises to the top of your list.'],
            ];
            foreach ($features as $i => $f): ?>
                <article data-reveal data-reveal-delay="<?= ($i % 3) + 1 ?>"
                         class="group rounded-2xl border border-solid border-white/10 bg-white/5 p-6 backdrop-blur-md transition-colors hover:border-brand-500/40 hover:bg-white/10">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-brand-600/20 text-brand-300 ring-1 ring-inset ring-brand-500/30 transition-transform group-hover:scale-110">
                        <?= icon($f[0], 'h-6 w-6') ?>
                    </div>
                    <h3 class="mt-5 text-lg font-semibold text-white"><?= $f[1] ?></h3>
                    <p class="mt-2 text-sm leading-relaxed text-surface-300"><?= $f[2] ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- ===================== CLOSING CTA ===================== -->
    <section class="px-4 pb-24">
        <div data-reveal class="relative mx-auto max-w-4xl overflow-hidden rounded-3xl border border-solid border-white/10 bg-gradient-to-br from-brand-600/20 via-surface-800 to-surface-800 p-10 text-center backdrop-blur-md sm:p-14">
            <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-brand-500/20 blur-3xl"></div>
            <h2 class="relative text-3xl font-bold text-white sm:text-4xl">Ready to follow through?</h2>
            <p class="relative mx-auto mt-4 max-w-xl text-surface-300">
                Add your first goal, start the timer, and watch your progress add up. It only takes a minute to begin.
            </p>
            <div class="relative mt-8">
                <button type="button" onclick="goToLogin()" class="btn-primary px-8 py-3 text-base">Get started — it’s free</button>
            </div>
        </div>
    </section>

    <?php include('app/Views/footer.view.php'); ?>
</body>

</html>
