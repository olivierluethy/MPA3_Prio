<?php
/**
 * Shared DURATION editor (not a time-of-day input).
 * Config (set before include):
 *   $durEditorId        unique id for this instance (default 'durationEditor')
 *   $durShowWindow      bool — show the optional start/end window slider
 *   $durInitialSeconds  int — pre-fill duration in seconds (default 0)
 *   $durName            name for the submitted HH:MM:SS hidden field (default 'time')
 * Driven by public/js/durationEditor.js; the value is a duration in seconds,
 * emitted as HH:MM:SS to match the existing model/endpoints.
 */
$durEditorId      = $durEditorId ?? 'durationEditor';
$durShowWindow    = !empty($durShowWindow);
$durInitialSeconds = isset($durInitialSeconds) ? (int) $durInitialSeconds : 0;
$durName          = $durName ?? 'time';
?>
<div id="<?= htmlspecialchars($durEditorId, ENT_QUOTES, 'UTF-8') ?>" class="duration-editor"
     data-duration-editor data-window="<?= $durShowWindow ? '1' : '0' ?>" data-initial-seconds="<?= $durInitialSeconds ?>">
    <span class="label">Duration</span>
    <div class="mt-1 flex flex-wrap items-center gap-2">
        <label class="flex items-center gap-1"><input type="number" min="0" max="23" inputmode="numeric" class="input w-16 text-center" data-dur="h" aria-label="Hours"><span class="text-sm text-surface-400">h</span></label>
        <label class="flex items-center gap-1"><input type="number" min="0" max="59" inputmode="numeric" class="input w-16 text-center" data-dur="m" aria-label="Minutes"><span class="text-sm text-surface-400">m</span></label>
        <label class="flex items-center gap-1"><input type="number" min="0" max="59" inputmode="numeric" class="input w-16 text-center" data-dur="s" aria-label="Seconds"><span class="text-sm text-surface-400">s</span></label>
        <span class="ml-1 text-sm text-surface-400">= <span data-dur-total class="font-medium text-surface-200">0s</span></span>
    </div>
    <p class="warning" data-dur-error style="display:none"></p>

    <?php if ($durShowWindow): ?>
    <div class="mt-3" data-window>
        <button type="button" data-window-toggle
                class="inline-flex items-center gap-1.5 text-xs font-medium text-brand-300 transition-colors hover:text-brand-200 focus:outline-none">
            <i class="fas fa-clock" aria-hidden="true"></i> <span data-window-toggle-label>Set a start / end window</span>
        </button>
        <div data-window-body style="display:none" class="mt-3 rounded-lg border border-solid border-surface-700 bg-surface-900 p-4">
            <div class="dur-slider" data-slider>
                <div class="dur-track"><div class="dur-fill" data-fill></div></div>
                <input type="range" class="dur-range" data-range="start" aria-label="Start time">
                <input type="range" class="dur-range" data-range="end" aria-label="End time">
            </div>
            <div class="mt-3 flex items-center justify-between gap-2 text-xs text-surface-300">
                <span>Start <span class="font-mono text-surface-100" data-start-label>—</span></span>
                <span>Duration <span class="font-mono text-surface-100" data-dur-total-2>0s</span></span>
                <span>End <span class="font-mono text-surface-100" data-end-label>—</span></span>
            </div>
            <button type="button" data-window-clear class="mt-3 text-xs text-surface-400 hover:text-surface-200 focus:outline-none">Remove window (keep duration only)</button>
        </div>
    </div>
    <?php endif; ?>

    <input type="hidden" name="<?= htmlspecialchars($durName, ENT_QUOTES, 'UTF-8') ?>" data-out="hhmmss">
</div>
