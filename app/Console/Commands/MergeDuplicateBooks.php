<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;

class MergeDuplicateBooks extends Command
{
    protected $signature   = 'books:merge-duplicates {--dry-run : Show what would be merged without making changes}';
    protected $description = 'Merge duplicate books (same title+author owned by the same user) into a single record';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        // Group books by user_id + lowercase(title) + lowercase(author)
        $duplicates = Book::select('user_id', DB::raw('LOWER(title) as ltitle'), DB::raw('LOWER(author) as lauthor'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('user_id', 'ltitle', 'lauthor')
            ->having('cnt', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('No duplicate books found.');
            return 0;
        }

        foreach ($duplicates as $dup) {
            $books = Book::where('user_id', $dup->user_id)
                ->whereRaw('LOWER(title) = ?', [$dup->ltitle])
                ->whereRaw('LOWER(author) = ?', [$dup->lauthor])
                ->orderBy('id', 'asc')
                ->get();

            $canonical = $books->first();
            $toDelete  = $books->skip(1)->values();

            $this->info("Canonical: [{$canonical->id}] {$canonical->title} by {$canonical->author} (user_id={$canonical->user_id})");

            foreach ($toDelete as $dupe) {
                $loanCount = Loan::where('book_id', $dupe->id)->count();
                $this->warn("  → Merging duplicate [{$dupe->id}] (has {$loanCount} loans) into [{$canonical->id}]");

                if (!$dryRun) {
                    // Re-point loans to canonical book
                    Loan::where('book_id', $dupe->id)->update(['book_id' => $canonical->id]);

                    // Detach genres and delete
                    $dupe->genres()->detach();
                    $dupe->delete();
                }
            }
        }

        if ($dryRun) {
            $this->line('');
            $this->warn('DRY RUN — no changes made. Remove --dry-run to apply.');
        } else {
            $this->info('Done. Duplicates merged.');
        }

        return 0;
    }
}
