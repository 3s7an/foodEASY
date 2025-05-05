<?php

namespace App\Console\Commands;

use App\Models\Recipe;
use Illuminate\Console\Command;

class UpdateRecipeTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-recipe-times';

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
        $recipes = Recipe::where('time', 0)->get();

        $c = 0;

        foreach($recipes as $rec){
            $rec->time = 15;
            $rec->save();

            $c++;
        }

        $this->info('Čas bol zmenený '.$c.' receptom');
    }
}
