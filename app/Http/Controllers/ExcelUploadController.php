<?php

namespace App\Http\Controllers;

use App\BusinessLogicLayer\managers\MentorsUploadManager;

class ExcelUploadController extends Controller
{
    public function mentorsUpload()
    {
        $mentorsUploadManager = new MentorsUploadManager();
        $mentorsUploadManager->fileImportMentors();
        return "Mentors upload from file has been completed";
    }

}
