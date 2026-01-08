import { cn } from '@/lib/utils';
import { ChevronFirst, ChevronLast, ChevronLeft, ChevronRight } from 'lucide-react';

interface FancyPaginationProps {
    currentPage: number;
    totalPages: number;
    onPageChange: (page: number) => void;
    className?: string;
}

export function FancyPagination({ currentPage, totalPages, onPageChange, className }: FancyPaginationProps) {
    const canGoFirst = currentPage > 1;
    const canGoPrevious = currentPage > 1;
    const canGoNext = currentPage < totalPages;
    const canGoLast = currentPage < totalPages;

    // Generate array of page numbers for animation
    const pageNumbers = Array.from({ length: totalPages }, (_, i) => i + 1);

    return (
        <div className={cn('flex justify-center px-5 pt-8', className)}>
            <div className="flex select-none items-center gap-3">
                {/* First Page */}
                <button
                    onClick={() => canGoFirst && onPageChange(1)}
                    disabled={!canGoFirst}
                    className={cn(
                        'grid h-10 w-10 place-items-center rounded-full border bg-muted/50 shadow-sm transition duration-300',
                        canGoFirst ? 'cursor-pointer hover:bg-muted' : 'cursor-not-allowed opacity-40'
                    )}
                    aria-label="First page"
                >
                    <ChevronFirst className="h-5 w-5" />
                </button>

                {/* Previous Page */}
                <button
                    onClick={() => canGoPrevious && onPageChange(currentPage - 1)}
                    disabled={!canGoPrevious}
                    className={cn(
                        'grid h-10 w-10 place-items-center rounded-full border bg-muted/50 shadow-sm transition duration-300',
                        canGoPrevious ? 'cursor-pointer hover:bg-muted' : 'cursor-not-allowed opacity-40'
                    )}
                    aria-label="Previous page"
                >
                    <ChevronLeft className="h-5 w-5" />
                </button>

                {/* Animated Page Number Display */}
                <div className="grid w-16 place-items-center">
                    <div className="relative h-12 w-14">
                        {pageNumbers.map((pageNum) => (
                            <div
                                key={pageNum}
                                className={cn(
                                    'absolute text-xl font-semibold transition-all duration-300',
                                    pageNum === currentPage && 'right-[60%] top-0',
                                    pageNum > currentPage && '-top-5 right-[20%] opacity-0',
                                    pageNum < currentPage && 'right-[90%] top-5 opacity-0'
                                )}
                            >
                                <span>{pageNum}</span>
                            </div>
                        ))}

                        {/* Diagonal line */}
                        <div className="absolute right-1/2 top-1/2 h-[0.5px] w-10 -translate-y-1/2 translate-x-1/2 -rotate-45 rounded-full bg-current" />

                        {/* Total pages */}
                        <div className="absolute bottom-1 left-[60%] text-sm text-muted-foreground">
                            {totalPages}
                        </div>
                    </div>
                </div>

                {/* Next Page */}
                <button
                    onClick={() => canGoNext && onPageChange(currentPage + 1)}
                    disabled={!canGoNext}
                    className={cn(
                        'grid h-10 w-10 place-items-center rounded-full border bg-muted/50 shadow-sm transition duration-300',
                        canGoNext ? 'cursor-pointer hover:bg-muted' : 'cursor-not-allowed opacity-40'
                    )}
                    aria-label="Next page"
                >
                    <ChevronRight className="h-5 w-5" />
                </button>

                {/* Last Page */}
                <button
                    onClick={() => canGoLast && onPageChange(totalPages)}
                    disabled={!canGoLast}
                    className={cn(
                        'grid h-10 w-10 place-items-center rounded-full border bg-muted/50 shadow-sm transition duration-300',
                        canGoLast ? 'cursor-pointer hover:bg-muted' : 'cursor-not-allowed opacity-40'
                    )}
                    aria-label="Last page"
                >
                    <ChevronLast className="h-5 w-5" />
                </button>
            </div>
        </div>
    );
}
