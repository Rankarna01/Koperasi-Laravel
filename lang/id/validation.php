<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'accepted'             => ':attribute harus diterima.',
    'accepted_if'          => ':attribute harus diterima jika :other adalah :value.',
    'active_url'           => ':attribute bukan URL yang valid.',
    'after'                => ':attribute harus setelah tanggal :date.',
    'after_or_equal'       => ':attribute harus setelah atau sama dengan tanggal :date.',
    'alpha'                => ':attribute hanya boleh berisi huruf.',
    'alpha_dash'           => ':attribute hanya boleh berisi huruf, angka, tanda strip, dan garis bawah.',
    'alpha_num'            => ':attribute hanya boleh berisi huruf dan angga.',
    'array'                => ':attribute harus berupa array.',
    'before'               => ':attribute harus sebelum tanggal :date.',
    'before_or_equal'      => ':attribute harus sebelum atau sama dengan tanggal :date.',
    'between'              => [
        'array'   => ':attribute harus memiliki :min - :max item.',
        'file'    => ':attribute harus berukuran antara :min - :max kilobyte.',
        'numeric' => ':attribute harus antara :min - :max.',
        'string'  => ':attribute harus antara :min - :max karakter.',
    ],
    'boolean'              => ':attribute harus bernilai benar atau salah.',
    'confirmed'            => ':attribute konfirmasi tidak cocok.',
    'date'                 => ':attribute bukan tanggal yang valid.',
    'date_equals'          => ':attribute harus sama dengan tanggal :date.',
    'date_format'          => ':attribute tidak cocok dengan format :format.',
    'different'            => ':attribute dan :other harus berbeda.',
    'digits'               => ':attribute harus berupa :digits digit.',
    'digits_between'       => ':attribute harus antara :min dan :max digit.',
    'dimensions'           => ':attribute memiliki dimensi gambar yang tidak valid.',
    'distinct'             => ':attribute memiliki nilai duplikat.',
    'email'                => ':attribute harus berupa alamat email yang valid.',
    'ends_with'            => ':attribute harus diakhiri dengan salah satu dari: :values.',
    'exists'               => ':attribute yang dipilih tidak valid.',
    'file'                 => ':attribute harus berupa file.',
    'filled'               => ':attribute wajib diisi.',
    'gt'                   => ':attribute harus lebih besar dari :value.',
    'gte'                  => ':attribute harus lebih besar atau sama dengan :value.',
    'image'                => ':attribute harus berupa gambar.',
    'in'                   => ':attribute yang dipilih tidak valid.',
    'in_array'             => ':attribute tidak ada di :other.',
    'integer'              => ':attribute harus berupa angka bulat.',
    'ip'                   => ':attribute harus berupa alamat IP yang valid.',
    'json'                 => ':attribute harus berupa string JSON.',
    'lt'                   => ':attribute harus lebih kecil dari :value.',
    'lte'                  => ':attribute harus lebih kecil atau sama dengan :value.',
    'max'                  => [
        'array'   => ':attribute tidak boleh lebih dari :max item.',
        'file'    => ':attribute tidak boleh lebih dari :max kilobyte.',
        'numeric' => ':attribute tidak boleh lebih dari :max.',
        'string'  => ':attribute tidak boleh lebih dari :max karakter.',
    ],
    'mimes'                => ':attribute harus berupa file tipe: :values.',
    'mimetypes'            => ':attribute harus berupa file tipe: :values.',
    'min'                  => [
        'array'   => ':attribute harus memiliki minimal :min item.',
        'file'    => ':attribute harus berukuran minimal :min kilobyte.',
        'numeric' => ':attribute harus minimal :min.',
        'string'  => ':attribute harus minimal :min karakter.',
    ],
    'not_in'               => ':attribute yang dipilih tidak valid.',
    'not_regex'            => ':attribute format tidak valid.',
    'numeric'              => ':attribute harus berupa angka.',
    'password'             => 'Kata sandi salah.',
    'present'              => ':attribute harus ada.',
    'regex'                => ':attribute format tidak valid.',
    'required'             => ':attribute wajib diisi.',
    'required_if'          => ':attribute wajib diisi jika :other adalah :value.',
    'required_unless'      => ':attribute wajib diisi kecuali :other adalah :values.',
    'required_with'        => ':attribute wajib diisi jika :values ada.',
    'required_with_all'    => ':attribute wajib diisi jika :values ada.',
    'required_without'     => ':attribute wajib diisi jika :values tidak ada.',
    'required_without_all' => ':attribute wajib diisi jika tidak ada :values yang ada.',
    'same'                 => ':attribute dan :other harus cocok.',
    'size'                 => [
        'array'   => ':attribute harus berisi :size item.',
        'file'    => ':attribute harus berukuran :size kilobyte.',
        'numeric' => ':attribute harus bernilai :size.',
        'string'  => ':attribute harus :size karakter.',
    ],
    'starts_with'          => ':attribute harus diawali dengan salah satu dari: :values.',
    'string'               => ':attribute harus berupa teks.',
    'timezone'             => ':attribute harus berupa zona waktu yang valid.',
    'unique'               => ':attribute sudah digunakan.',
    'uploaded'             => ':attribute gagal diunggah.',
    'url'                  => ':attribute format URL tidak valid.',
    'uuid'                 => ':attribute harus berupa UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'custom' => [],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'name'                  => 'Nama',
        'email'                 => 'Email',
        'password'              => 'Kata Sandi',
        'password_confirmation' => 'Konfirmasi Kata Sandi',
        'nominal'               => 'Nominal',
        'metode_pembayaran'     => 'Metode Pembayaran',
        'keterangan'            => 'Keterangan',
        'rekening_bank.nama_bank'     => 'Nama Bank',
        'rekening_bank.no_rekening'   => 'Nomor Rekening',
        'rekening_bank.nama_rekening' => 'Nama Pemilik Rekening',
        'app_name'              => 'Nama Koperasi',
        'company_address'       => 'Alamat',
        'app_logo'              => 'Logo',
        'minimal_saldo_pokok'   => 'Minimal Saldo Pokok',
        'iuran_wajib_bulanan'   => 'Iuran Wajib Bulanan',
        'bunga_simpanan_persen' => 'Bunga Simpanan',
    ],

];
