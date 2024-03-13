<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    |
    |
    */

    'accepted' => ':attribute باید پذیرفته شود.',
    'active_url' => ':attribute یک URL معتبر نیست.',
    'after' => ':attribute باید یک تاریخ پس از :date  باشد.',
    'after_or_equal' => ':attribute  باید یک تاریخ پس از یا مساوی با: تاریخ باشد.',
    'alpha' => ':attribute  ممکن است فقط شامل حروف باشد.',
    'alpha_dash' => ':attribute ممکن است فقط شامل حروف، اعداد، خط و تاکید زیرین خط باشد.',
    'alpha_num' => ':attribute ممکن است فقط شامل حروف و اعداد باشد.',
    'array' => ':attribute  باید شامل :size باشد.',
    'before' => ':attribute باید یک روز قبل از  :date باشد.',
    'before_or_equal' => ':attribute  باید یک روز قبل یا مساوی با   :date  باشد.',
    'between' => [
        'numeric' => ':attribute  باید :size باشد.',
        'file' => ':attribute  باید  :size  کیلوبایت باشد.',
        'string' => ':attribute  باید یک رشته باشد.',
        'array' => ' :attribute باید بین  :min و :max مورد باشد.',
    ],
    'boolean' => 'فیلد :attribute باید درست یا نادرست باشد.',
    'confirmed' => 'تایید :attribute مطابقت ندارد.',
    'date' => ':attribute  یک تاریخ معتبر نیست.',
    'date_equals' => ':attribute باید یک تاریخ مساوی با :date باشد.',
    'date_format' => ':attribute با :format مطابقت ندارد.',
    'different' => ':attribute و  :other باید متفاوت باشند.',
    'digits' => ':attribute  باید :digits  رقم  باشند.',
    'digits_between' => ':attribute  باید بین  :min  و  :max رقم  باشد.',
    'dimensions' => ':attribute  دارای ابعاد تصویر نامعتبر است.',
    'distinct' => ':attribute  دارای یک مقدار تکراری است.',
    'email' => ':attribute  باید یک آدرس ایمیل معتبر باشد.',
    'ends_with' => ':attribute  باید با یکی از موارد زیر پایان یابد :values',
    'exists' => 'انتخاب شده  :attribute  نامعتبر است.',
    'file' => 'The :attribute must be a file.',
    'filled' => ':attribute  باید دارای یک مقدار باشد.',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file' => 'The :attribute must be greater than or equal :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => ':attribute  باید بک تصویر باشد.',
    'in' => ':attribute  انتخاب شده نامعتبر است.',
    'in_array' => 'قسمت  :attribute  وجود ندارد در: دیگری',
    'integer' => ':attribute  باید یک عدد صحیح باشد.',
    'ip' => ':attribute  باید یک آدرس IP معتبر باشد.',
    'ipv4' => ':attribute باید یک آدرس IPv4 معتبر باشد.',
    'ipv6' => ':attribute  باید یک آدرس IPv6 معتبر باشد.',
    'json' => ':attribute  باید یک رشتۀ JSON معتبر باشد.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file' => 'The :attribute must be less than or equal :value kilobytes.',
        'string' => 'The :attribute must be less than or equal :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file' => 'The :attribute may not be greater than :max kilobytes.',
        'string' => 'The :attribute may not be greater than :max characters.',
        'array' => 'The :attribute may not have more than :max items.',
    ],
    'mimes' => ':attribute  باید فایلی از نوع: :values باشد.',
    'mimetypes' => ':attribute  باید فایلی از نوع:  :values باشد.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'not_in' => ':attribute انتخاب شده: نامعتبر است.',
    'not_regex' => 'فرمت  :attribute نامعتبر است.',
    'numeric' => 'The :attribute must be a number.',
    'password' => 'رمز عبور نادرست است.',
    'password_or_username' => 'رمز عبور یا نام کاربری نادرست است.',
    'present' => ':attribute  باید حاضر باشد.',
    'regex' => 'فرمت  :attribute نامعتبر است.',
    'required' => 'فیلد  :attribute  مورد نیاز است.',
    'required_if' => 'فیلد  :attribute  مورد نیاز است وقتی: دیگری است: مقدار.',
    'required_unless' => 'فیلد  :attribute  مورد نیاز است مگر اینکه  :other در :values  است.',
    'required_with' => ':attribute  مورد نیاز است وقتی :values حاضر باشند.',
    'required_with_all' => ':attribute  مورد نیاز است وقتی :values حاضر باشند.',
    'required_without' => ':attribute  مورد نیاز است وقتی :values حاضر نباشند.',
    'required_without_all' => ':attribute مورد نیاز است وقتی هیچ یک از:values حاضر باشند.',
    'same' => ':attribute  و: دیگری باید مطابقت داشته باشند.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'starts_with' => ':attribute  باید با یکی از مقادیر:values  آغاز شود',
    'string' => 'The :attribute must be a string.',
    'timezone' => ':attribute  باید یک منطقۀ زمانی معتبر باشد.',
    'unique' => ':attribute  قبلاٌ گرفته شده است.',
    'uploaded' => ':attribute  بارگذاری نشد.',
    'url' => 'فرمت  :attribute  نامعتبر است.',
    'uuid' => ':attribute باید یکUUID معتبر باشد.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    'captcha' => 'کلید امنیتی صحیح نیست ...',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
