<?php

namespace App\Livewire\User;

use App\Models\ActivityLog;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Gif\Builder as GifBuilder;
use iio\libmergepdf\Merger;
use Livewire\Component;
use Livewire\WithFileUploads;
use setasign\Fpdi\Fpdi;

class PdfTools extends Component
{
    use WithFileUploads;

    public function recordHistory(string $toolName, string $details)
    {
        $item = [
            'tool' => $toolName,
            'details' => $details,
            'time' => now()->format('Y-m-d H:i:s'),
        ];
        $history = session()->get('pdf_tools_history', []);
        array_unshift($history, $item);
        $history = array_slice($history, 0, 15); // keep last 15
        session()->put('pdf_tools_history', $history);

        if (auth()->check()) {
            ActivityLog::record('pdf_tool_used', auth()->user(), "Used {$toolName}: {$details}");
        }
    }

    public function clearHistory()
    {
        session()->forget('pdf_tools_history');
        session()->flash('success', 'Tool history cleared.');
    }

    public $activeTool = 'compress'; // compress, resize, crop, convert, editor, all

    public function setTool($tool)
    {
        $this->activeTool = $tool;
    }

    // PDF Maker
    public $pdfTitle = '';
    public $pdfContent = '';

    // Image Compressor
    public $compressorImage = null;
    public $compressorQuality = 75; // 0-100

    // Image Resizer
    public $resizerImage = null;
    public $resizerWidth = 800;
    public $resizerHeight = null;
    public $resizerMaintainAspect = true;

    // Image Converter
    public $converterImage = null;
    public $converterFormat = 'webp'; // webp, jpeg, png

    // Images to PDF
    public $imagesToPdfFiles = [];
    public $imagesToPdfTitle = 'Document';

    // PDF Merger
    public $pdfMergerFiles = [];

    // PDF Splitter
    public $pdfSplitterFile = null;
    public $splitFromPage = 1;
    public $splitToPage = 1;

    // PDF Rotator
    public $pdfRotatorFile = null;
    public $rotationDegrees = 90; // 90, 180, 270

    // PDF Watermarker
    public $pdfWatermarkerFile = null;
    public $watermarkText = '';
    public $watermarkColor = '#000000';
    public $watermarkOpacity = 50; // 0-100

    // PDF to Images
    public $pdfToImagesFile = null;
    public $pdfToImagesFormat = 'png';

    // PDF Password
    public $pdfPasswordFile = null;
    public $pdfPassword = '';

    // Batch Image Processor
    public $batchImages = [];
    public $batchAction = 'compress'; // compress, resize, convert
    public $batchQuality = 75;
    public $batchWidth = 800;
    public $batchFormat = 'webp';

    // Image Cropper
    public $cropperImage = null;
    public $cropX = 0;
    public $cropY = 0;
    public $cropWidth = 200;
    public $cropHeight = 200;

    // Image Filters
    public $filterImage = null;
    public $filterType = 'grayscale'; // grayscale, sepia, brightness, contrast
    public $brightnessLevel = 0; // -100 to 100
    public $contrastLevel = 0; // -100 to 100

    // GIF Maker
    public $gifImages = [];
    public $gifDelay = 100; // delay between frames in milliseconds
    public $gifLoop = 0; // 0 = infinite loop

    public function generatePdf()
    {
        $this->validate([
            'pdfTitle' => 'required|string',
            'pdfContent' => 'required|string',
        ]);

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $html = <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body {
                    font-family: 'DejaVu Sans', sans-serif;
                    padding: 20px;
                }
                h1 {
                    color: #4A5D4A;
                }
            </style>
        </head>
        <body>
            <h1>{$this->pdfTitle}</h1>
            <div>{$this->pdfContent}</div>
        </body>
        </html>
        HTML;

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $this->recordHistory('PDF Maker', "Generated PDF document '{$this->pdfTitle}'");

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, $this->pdfTitle . '.pdf');
    }

    public function compressImage()
    {
        if (!$this->compressorImage) {
            session()->flash('error', 'Please select an image to compress');
            return;
        }

        $this->validate([
            'compressorImage' => 'required|file',
        ]);

        $filePath = $this->compressorImage->store('temp', 'local');
        $fullPath = storage_path('app/' . $filePath);

        $img = Image::read($fullPath);
        
        $extension = $this->compressorImage->guessExtension();
        $compressedFileName = 'compressed_' . $this->compressorImage->getClientOriginalName();
        $compressedPath = storage_path('app/public/' . $compressedFileName);

        $quality = max(10, min(100, intval($this->compressorQuality)));

        if (in_array($extension, ['jpeg', 'jpg'])) {
            $img->toJpeg(quality: $quality)->save($compressedPath);
        } elseif ($extension == 'png') {
            $img->toPng()->save($compressedPath);
        } elseif ($extension == 'webp') {
            $img->toWebp(quality: $quality)->save($compressedPath);
        } else {
            $img->save($compressedPath);
        }

        Storage::delete($filePath);

        $this->recordHistory('Image Compressor', "Compressed image at {$quality}% quality");

        return response()->download($compressedPath, $compressedFileName)->deleteFileAfterSend(true);
    }

    public function resizeImage()
    {
        if (!$this->resizerImage) {
            session()->flash('error', 'Please upload an image to resize');
            return;
        }

        $this->validate([
            'resizerImage' => 'required|file',
        ]);

        $filePath = $this->resizerImage->store('temp', 'local');
        $fullPath = storage_path('app/' . $filePath);

        $img = Image::read($fullPath);
        
        $width = intval($this->resizerWidth);
        $height = $this->resizerHeight ? intval($this->resizerHeight) : null;

        if ($this->resizerMaintainAspect) {
            $img->scale($width);
        } else {
            $img->resize($width, $height);
        }

        $extension = $this->resizerImage->guessExtension();
        $resizedFileName = 'resized_' . $this->resizerImage->getClientOriginalName();
        $resizedPath = storage_path('app/public/' . $resizedFileName);

        $img->save($resizedPath);

        Storage::delete($filePath);

        return response()->download($resizedPath, $resizedFileName)->deleteFileAfterSend(true);
    }

    public function convertImage()
    {
        if (!$this->converterImage) {
            session()->flash('error', 'Please upload an image to convert');
            return;
        }

        $this->validate([
            'converterImage' => 'required|file',
        ]);

        $filePath = $this->converterImage->store('temp', 'local');
        $fullPath = storage_path('app/' . $filePath);

        $img = Image::read($fullPath);

        $extension = $this->converterFormat;
        $originalName = pathinfo($this->converterImage->getClientOriginalName(), PATHINFO_FILENAME);
        $convertedFileName = $originalName . '.' . $extension;
        $convertedPath = storage_path('app/public/' . $convertedFileName);

        if ($extension == 'jpeg' || $extension == 'jpg') {
            $img->toJpeg(quality: 85)->save($convertedPath);
        } elseif ($extension == 'png') {
            $img->toPng()->save($convertedPath);
        } elseif ($extension == 'webp') {
            $img->toWebp(quality: 85)->save($convertedPath);
        } else {
            $img->save($convertedPath);
        }

        Storage::delete($filePath);

        $this->recordHistory('Image Converter', "Converted image to {$extension} format");

        return response()->download($convertedPath, $convertedFileName)->deleteFileAfterSend(true);
    }

    public function imagesToPdf()
    {
        if (empty($this->imagesToPdfFiles)) {
            session()->flash('error', 'Please upload images to convert to PDF');
            return;
        }

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        $html = '<!DOCTYPE html><html><head><style>img{max-width:100%;height:auto;page-break-after:always;}</style></head><body>';

        foreach ($this->imagesToPdfFiles as $file) {
            $filePath = $file->store('temp', 'local');
            $fullPath = storage_path('app/' . $filePath);
            $imageData = base64_encode(file_get_contents($fullPath));
            $mime = $file->getMimeType();
            $html .= "<img src='data:{$mime};base64,{$imageData}'>";
            Storage::delete($filePath);
        }

        $html .= '</body></html>';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, $this->imagesToPdfTitle . '.pdf');
    }

    public function mergePdfs()
    {
        if (count($this->pdfMergerFiles) < 2) {
            session()->flash('error', 'Please upload at least 2 PDF files to merge');
            return;
        }

        $merger = new Merger();
        $tempFiles = [];

        foreach ($this->pdfMergerFiles as $file) {
            $filePath = $file->store('temp', 'local');
            $fullPath = storage_path('app/' . $filePath);
            $merger->addFile($fullPath);
            $tempFiles[] = $filePath;
        }

        $mergedContent = $merger->merge();
        $mergedFileName = 'merged_document_' . time() . '.pdf';
        $mergedPath = storage_path('app/public/' . $mergedFileName);
        file_put_contents($mergedPath, $mergedContent);

        foreach ($tempFiles as $filePath) {
            Storage::delete($filePath);
        }

        $this->recordHistory('PDF Merger', "Merged " . count($this->pdfMergerFiles) . " PDF files");

        return response()->download($mergedPath, $mergedFileName)->deleteFileAfterSend(true);
    }

    public function splitPdf()
    {
        if (!$this->pdfSplitterFile) {
            session()->flash('error', 'Please upload a PDF file to split');
            return;
        }

        $filePath = $this->pdfSplitterFile->store('temp', 'local');
        $fullPath = storage_path('app/' . $filePath);

        try {
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($fullPath);
            
            $from = max(1, intval($this->splitFromPage));
            $to = min($pageCount, intval($this->splitToPage));
            
            if ($from > $to) {
                $temp = $from;
                $from = $to;
                $to = $temp;
            }

            $pdf->AddPage();
            for ($i = $from; $i <= $to; $i++) {
                $template = $pdf->importPage($i);
                $pdf->useTemplate($template);
                if ($i < $to) {
                    $pdf->AddPage();
                }
            }

            $splitFileName = 'split_document_' . time() . '.pdf';
            $splitPath = storage_path('app/public/' . $splitFileName);
            $pdf->Output('F', $splitPath);
            
            Storage::delete($filePath);
            return response()->download($splitPath, $splitFileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Storage::delete($filePath);
            session()->flash('error', 'Error splitting PDF: ' . $e->getMessage());
        }
    }

    public function rotatePdf()
    {
        if (!$this->pdfRotatorFile) {
            session()->flash('error', 'Please upload a PDF file to rotate');
            return;
        }

        $filePath = $this->pdfRotatorFile->store('temp', 'local');
        $fullPath = storage_path('app/' . $filePath);

        try {
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($fullPath);
            
            for ($i = 1; $i <= $pageCount; $i++) {
                $template = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($template);
                
                $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                
                $pdf->useTemplate($template, 0, 0, $size['width'], $size['height'], false, intval($this->rotationDegrees));
            }

            $rotatedFileName = 'rotated_document_' . time() . '.pdf';
            $rotatedPath = storage_path('app/public/' . $rotatedFileName);
            $pdf->Output('F', $rotatedPath);
            
            Storage::delete($filePath);
            return response()->download($rotatedPath, $rotatedFileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Storage::delete($filePath);
            session()->flash('error', 'Error rotating PDF: ' . $e->getMessage());
        }
    }

    public function watermarkPdf()
    {
        if (!$this->pdfWatermarkerFile || !$this->watermarkText) {
            session()->flash('error', 'Please upload a PDF file and enter watermark text');
            return;
        }

        $filePath = $this->pdfWatermarkerFile->store('temp', 'local');
        $fullPath = storage_path('app/' . $filePath);

        try {
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($fullPath);
            
            for ($i = 1; $i <= $pageCount; $i++) {
                $template = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($template);
                
                $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                $pdf->useTemplate($template);
                
                // Add watermark
                $pdf->SetFont('Arial', 'B', 50);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetAlpha($this->watermarkOpacity / 100);
                
                $pdf->Rotate(45, $size['width'] / 2, $size['height'] / 2);
                $pdf->Text($size['width'] / 2 - 100, $size['height'] / 2, $this->watermarkText);
                $pdf->Rotate(0);
                $pdf->SetAlpha(1);
            }

            $watermarkedFileName = 'watermarked_document_' . time() . '.pdf';
            $watermarkedPath = storage_path('app/public/' . $watermarkedFileName);
            $pdf->Output('F', $watermarkedPath);
            
            Storage::delete($filePath);
            return response()->download($watermarkedPath, $watermarkedFileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Storage::delete($filePath);
            session()->flash('error', 'Error adding watermark: ' . $e->getMessage());
        }
    }

    public function batchProcessImages()
    {
        if (empty($this->batchImages)) {
            session()->flash('error', 'Please upload images to process');
            return;
        }

        // Create a temporary directory for processed images
        $tempDir = 'batch_' . time();
        $zipPath = storage_path('app/public/' . $tempDir . '.zip');
        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach ($this->batchImages as $file) {
            $filePath = $file->store('temp', 'local');
            $fullPath = storage_path('app/' . $filePath);

            $img = Image::read($fullPath);

            switch ($this->batchAction) {
                case 'compress':
                    $quality = max(10, min(100, intval($this->batchQuality)));
                    $extension = $file->guessExtension();
                    $processedFileName = 'compressed_' . $file->getClientOriginalName();
                    $processedPath = storage_path('app/temp/' . $processedFileName);

                    if (in_array($extension, ['jpeg', 'jpg'])) {
                        $img->toJpeg(quality: $quality)->save($processedPath);
                    } elseif ($extension == 'png') {
                        $img->toPng()->save($processedPath);
                    } elseif ($extension == 'webp') {
                        $img->toWebp(quality: $quality)->save($processedPath);
                    } else {
                        $img->save($processedPath);
                    }
                    break;
                
                case 'resize':
                    $width = intval($this->batchWidth);
                    $img->scale($width);
                    $processedFileName = 'resized_' . $file->getClientOriginalName();
                    $processedPath = storage_path('app/temp/' . $processedFileName);
                    $img->save($processedPath);
                    break;
                
                case 'convert':
                    $extension = $this->batchFormat;
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $processedFileName = $originalName . '.' . $extension;
                    $processedPath = storage_path('app/temp/' . $processedFileName);

                    if ($extension == 'jpeg' || $extension == 'jpg') {
                        $img->toJpeg(quality: 85)->save($processedPath);
                    } elseif ($extension == 'png') {
                        $img->toPng()->save($processedPath);
                    } elseif ($extension == 'webp') {
                        $img->toWebp(quality: 85)->save($processedPath);
                    } else {
                        $img->save($processedPath);
                    }
                    break;
            }

            $zip->addFile($processedPath, $processedFileName);
            Storage::delete($filePath);
            @unlink($processedPath);
        }

        $zip->close();
        return response()->download($zipPath, $tempDir . '.zip')->deleteFileAfterSend(true);
    }

    public function cropImage()
    {
        if (!$this->cropperImage) {
            session()->flash('error', 'Please upload an image to crop');
            return;
        }

        $this->validate([
            'cropperImage' => 'required|file',
        ]);

        $filePath = $this->cropperImage->store('temp', 'local');
        $fullPath = storage_path('app/' . $filePath);

        $img = Image::read($fullPath);
        $img->crop($this->cropWidth, $this->cropHeight, $this->cropX, $this->cropY);

        $croppedFileName = 'cropped_' . $this->cropperImage->getClientOriginalName();
        $croppedPath = storage_path('app/public/' . $croppedFileName);
        $img->save($croppedPath);

        Storage::delete($filePath);
        return response()->download($croppedPath, $croppedFileName)->deleteFileAfterSend(true);
    }

    public function applyFilter()
    {
        if (!$this->filterImage) {
            session()->flash('error', 'Please upload an image to filter');
            return;
        }

        $this->validate([
            'filterImage' => 'required|file',
        ]);

        $filePath = $this->filterImage->store('temp', 'local');
        $fullPath = storage_path('app/' . $filePath);

        $img = Image::read($fullPath);

        switch ($this->filterType) {
            case 'grayscale':
                $img->greyscale();
                break;
            case 'sepia':
                $img->greyscale()->colorize(100, 50, 0);
                break;
            case 'brightness':
                $img->brightness($this->brightnessLevel);
                break;
            case 'contrast':
                $img->contrast($this->contrastLevel);
                break;
        }

        $filteredFileName = 'filtered_' . $this->filterImage->getClientOriginalName();
        $filteredPath = storage_path('app/public/' . $filteredFileName);
        $img->save($filteredPath);

        Storage::delete($filePath);
        return response()->download($filteredPath, $filteredFileName)->deleteFileAfterSend(true);
    }

    public function makeGif()
    {
        if (count($this->gifImages) < 2) {
            session()->flash('error', 'Please upload at least 2 images to make a GIF');
            return;
        }

        $tempPaths = [];
        $gifBuilder = GifBuilder::canvas();

        foreach ($this->gifImages as $file) {
            $filePath = $file->store('temp', 'local');
            $fullPath = storage_path('app/' . $filePath);

            // Resize images to have consistent dimensions
            $img = Image::read($fullPath);
            $img->scale(400);
            $resizedPath = storage_path('app/temp/gif_frame_' . uniqid() . '.png');
            $img->toPng()->save($resizedPath);

            $tempPaths[] = $resizedPath;
            $gifBuilder->addFrame($resizedPath, $this->gifDelay);
            Storage::delete($filePath);
        }

        $gifBuilder->setLoops($this->gifLoop);
        $gifPath = storage_path('app/public/gif_' . time() . '.gif');
        $gifBuilder->save($gifPath);

        // Cleanup
        foreach ($tempPaths as $path) {
            @unlink($path);
        }

        return response()->download($gifPath, 'animated.gif')->deleteFileAfterSend(true);
    }

    public function render()
    {
        return view('livewire.user.pdf-tools');
    }
}
