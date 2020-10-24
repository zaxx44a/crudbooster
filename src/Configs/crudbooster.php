<?php

return [

    /**
     * This setting is for prefix of backend path.
     * For without prefix, you can set it to NULL.
     */
    'ADMIN_PATH' => 'admin',

    /**
     * Login credential columns
     */
    'USERNAME' => [
        'column'=> 'email',
        'label'=> 'Email',
        'validation'=> 'required|email|exists:users'
    ],
    'PASSWORD' => [
        'column'=> 'password',
        'label'=> 'Password',
        'validation'=> 'required|string'
    ],

    /**
     * Module Generator Settings
     */
    'IMAGE_FIELDS_CANDIDATE' => ['image','picture','photo','photos','foto','gambar','thumbnail'],
    'FILE_FIELDS_CANDIDATE' => ['file','upload','document','dokumen','download','lampiran','attachment'],
    'PASSWORD_FIELDS_CANDIDATE' => ['password','pass','pwd','passwrd','sandi','pin'],
    'DATE_FIELDS_CANDIDATE' => ['date','tanggal','tgl','created_at','updated_at','deleted_at'],
    'EMAIL_FIELDS_CANDIDATE' => ['email','mail','email_address'],
    'PHONE_FIELDS_CANDIDATE' => ['phone','phonenumber','phone_number','telp','hp','no_hp','no_telp'],
    'NAME_FIELDS_CANDIDATE' => ['name','nama','person_name','person','fullname','full_name','nickname','nick','nick_name','title','judul','content'],
    'URL_FIELDS_CANDIDATE' => ['url','link'],
    'TEXT_AREA_FIELDS_CANDIDATE'=> ['content','description','deskripsi','konten','isi','article','biography','bio'],
    'GENDER_FIELDS_CANDIDATE'=> ['gender','kelamin','sex'],

    /**
     * Security Section
     */
    'FILE_EXTENSION_ALLOWED' => ['doc','docx','xls','xlsx','csv','txt','pdf','zip','rar','gzip'],
    'IMAGE_EXTENSION_ALLOWED' => ['jpg','jpeg','png','gif','bmp','tiff'],
    'MAX_UPLOAD_SIZE' => 2000,
    'API_USER_AGENT_ALLOWED' => ['okhttp','android','ios','postman'],

    /**
     * Google API Key
     */
    'GOOGLE_FCM_KEY'=> null,
    'GOOGLE_MAP_KEY'=> null,
];