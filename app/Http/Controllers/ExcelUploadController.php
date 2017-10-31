<?php

namespace App\Http\Controllers;

use App\BusinessLogicLayer\managers\MenteesUploadManager;
use App\BusinessLogicLayer\managers\MentorsUploadManager;
use App\BusinessLogicLayer\managers\UpdateCreatedAtDateForMentees;
use App\BusinessLogicLayer\managers\UpdateSpecialtyForOldMentees;

class ExcelUploadController extends Controller
{
    public function mentorsUpload()
    {
        $mentorsUploadManager = new MentorsUploadManager();
        $mentorsUploadManager->fileImportMentors();
        return "Mentors upload from file has been completed";
    }

    public function menteesUpload()
    {
        $menteesUploadManager = new MenteesUploadManager();
        $menteesUploadManager->fileImportMentees();
        return "Mentees upload from file has been completed";
    }

    public function menteesUploadUpdate()
    {
        $updateSpecialtyForOldMentees = new UpdateSpecialtyForOldMentees();
        $updateSpecialtyForOldMentees->updateMentees();
        return "Mentees updated";
    }

    public function correctMenteesFromFileCreatedAt() {
        $updateCreatedAtDateForMentees = new UpdateCreatedAtDateForMentees();
        $updateCreatedAtDateForMentees->fileUpdateMentees();
        return "Mentees created_at updated";
    }
}
