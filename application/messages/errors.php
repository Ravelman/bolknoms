<?php defined('SYSPATH') or die('No direct script access.');

/*
 * Custom errors for form field validation
 */
return array(
  'date' => array(
                  'not_empty'                  => 'Je moet een datum invullen',
                  'free_day'                   => 'Op deze dag is al een maaltijd',
                  'Model_Meal::today_or_later' => 'Je kunt geen maaltijden in het verleden toevoegen',
                  'date'                       => 'Je moet een geldige datum invoeren (yyyy-mm-dd)'
                ),
  'name' => array(
                  'not_empty' => 'Je moet je naam invullen',
                  'regex' => 'Je naam mag alleen uit hoofdletters (A-Z), kleine letters (a-z), streepjes (-) en spaties bestaan'
                ),
  'email' => array(
                  'not_empty' => 'Je moet je e-mailadres invullen'
                ),
  'meals' => array(
                  'not_empty' => 'Je moet minstens één maaltijd selecteren'
                ),
  'locked' => array(
                  'not_empty'  => 'Je moet een tijd invullen waarop de inschrijving sluit',
                  'valid_time' => 'Je moet een geldige tijd invullen waarop de inschrijving sluit (HH:MM)'
                ),
);