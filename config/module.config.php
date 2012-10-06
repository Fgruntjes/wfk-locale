<?php
return array(
    'wfklocale' => array(
        'enabled' => array(
            'en_GB' => 'en',
            'nl_NL' => 'nl',
        ),
        'default_locale' => 'en_GB',
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type'     => 'PhpArray',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.php',
                'text_domain' => 'wfklocale'
            ),
        ),
    ),
);
