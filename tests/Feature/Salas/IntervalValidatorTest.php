<?php

use App\Reservations\IntervalValidator;

test('validator rejects an end that is not after the start', function () {
    $this->travelTo('2026-03-10 12:00:00');

    $validator = new IntervalValidator;

    expect($validator->firstFailure('2026-03-10', '10:00', '10:00'))
        ->toBe('Horário de fim deve ser posterior ao horário de início')
        ->and($validator->firstFailure('2026-03-10', '11:00', '10:00'))
        ->toBe('Horário de fim deve ser posterior ao horário de início');
});

test('validator rejects a date before today', function () {
    $this->travelTo('2026-03-10 12:00:00');

    $failure = (new IntervalValidator)->firstFailure('2026-03-09', '09:00', '10:00');

    expect($failure)->toBe('A data não pode ser anterior a hoje');
});

test('validator rejects an interval outside business hours', function () {
    $this->travelTo('2026-03-10 12:00:00');

    $validator = new IntervalValidator;

    expect($validator->firstFailure('2026-03-10', '07:59', '09:00'))
        ->toBe('Reservas permitidas apenas entre 8h e 18h')
        ->and($validator->firstFailure('2026-03-10', '17:00', '18:01'))
        ->toBe('Reservas permitidas apenas entre 8h e 18h');
});

test('validator rejects a duration over 4 hours', function () {
    $this->travelTo('2026-03-10 12:00:00');

    $failure = (new IntervalValidator)->firstFailure('2026-03-10', '09:00', '13:01');

    expect($failure)->toBe('Duração máxima permitida é de 4 horas');
});

test('validator reports only the first failing check', function () {
    $this->travelTo('2026-03-10 12:00:00');

    $validator = new IntervalValidator;

    expect($validator->firstFailure('2026-03-09', '19:00', '07:00'))
        ->toBe('Horário de fim deve ser posterior ao horário de início')
        ->and($validator->firstFailure('2026-03-09', '07:00', '19:00'))
        ->toBe('A data não pode ser anterior a hoje')
        ->and($validator->firstFailure('2026-03-10', '07:00', '19:00'))
        ->toBe('Reservas permitidas apenas entre 8h e 18h');
});

test('validator accepts a duration of exactly 4 hours', function () {
    $this->travelTo('2026-03-10 12:00:00');

    $failure = (new IntervalValidator)->firstFailure('2026-03-10', '09:00', '13:00');

    expect($failure)->toBeNull();
});

test('validator accepts a start of 08:00', function () {
    $this->travelTo('2026-03-10 12:00:00');

    $failure = (new IntervalValidator)->firstFailure('2026-03-10', '08:00', '12:00');

    expect($failure)->toBeNull();
});

test('validator accepts an end of 18:00', function () {
    $this->travelTo('2026-03-10 12:00:00');

    $failure = (new IntervalValidator)->firstFailure('2026-03-10', '14:00', '18:00');

    expect($failure)->toBeNull();
});
