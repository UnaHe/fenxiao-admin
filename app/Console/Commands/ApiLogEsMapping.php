<?php

namespace App\Console\Commands;

use App\Helpers\EsHelper;
use Illuminate\Console\Command;

class ApiLogEsMapping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apilog_es_mapping:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建apilog索引mapping';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //删除索引
        (new EsHelper())->client()->indices()->delete([
            'index' => 'apilog',
        ]);

        //创建索引
        (new EsHelper())->client()->indices()->create([
            'index' => 'apilog',
            'body' =>[
                'mappings' => [
                    'apilog' => [
                        "properties"=> [
                            "request_get"=> [
                                "type"=> "text"
                            ],
                            "request_header"=> [
                                "type"=> "text"
                            ],
                            "request_ip"=> [
                                "type"=> "text"
                            ],
                            "request_method"=> [
                                "type"=> "text"
                            ],
                            "request_post"=> [
                                "type"=> "text"
                            ],
                            "request_time"=> [
                                "type"=> "date",
                                "ignore_malformed"=>  true,
                                "format"=>  "yyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis"
                            ],
                            "request_uri"=> [
                                "type"=> "text"
                            ],
                            "response_content"=> [
                                "type"=> "text"
                            ],
                            "response_header"=> [
                                "type"=> "text"
                            ],
                            "response_status"=> [
                                "type"=> "integer"
                            ],
                            "response_time"=> [
                                "type"=>  "date",
                                "ignore_malformed"=>  true,
                                "format"=> "yyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis"
                            ],
                            "spend_time"=> [
                                "type"=> "float"
                            ],
                        ]
                    ]
                ]
            ]
        ]);

    }
}
