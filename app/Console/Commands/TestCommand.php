<?php

namespace App\Console\Commands;

use App\Services\BinaryNode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Mail::raw('This email confirms that everything was set up correctly!', function ($message) {
        //     $message->to('e.medjesi@gmail.com')
        //         ->subject('Testing Laravel + Mailgun');
        // });
        $t = [];
        $tree = new BinaryNode(10);
        $tree->left = new BinaryNode(5);
        $tree->right = new BinaryNode(15);
        $tree->left->left = new BinaryNode(2);
        $tree->left->right = new BinaryNode(7);
        $tree->right->left = new BinaryNode(12);
        $tree->right->right = new BinaryNode(20);
        $tree->print($tree,0,$t);
        $a = $tree->byLevels($tree);
        dump($a);
        dump($t);
    }
}
