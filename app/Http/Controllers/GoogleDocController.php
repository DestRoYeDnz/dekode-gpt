<?php

namespace App\Http\Controllers;

use \Google_Client;
use \Google_Service_Docs;
use Illuminate\Http\Request;

class GoogleDocController extends Controller
{
    public function createGoogleDoc(Request $request)
    {
        // $client = new \Google_Client();
        // $client->setApplicationName('Google Docs API');
        // $client->addScope(Google_Service_Docs::DOCUMENTS);
        // $client->setAccessType('offline');
        // // credentials.json is the key file we downloaded while setting up our Google Sheets API
        // $path = '/home/brettj/projects/laravel-inertia-chatgpt/storage/framework/sa.json';
        // $client->setAuthConfig($path);

        // // Create a new Google Docs service
        // $service = new \Google_Service_Docs($client);

        // // Create a new Google Doc
        // $doc = new \Google_Service_Docs_Document();
        // $doc->setTitle('New Document from GPT');

        // // Convert Markdown to Google Docs content
        // $markdownContent = 'markdown_content';
        // $convertedContent = $this->convertMarkdownToGoogleDocs($markdownContent);

        // // Insert the content into the Google Doc
        // $service->documents->batchUpdate($doc->documentId, $convertedContent);


        $client = new \Google_Client();
        $client->setApplicationName('Google Sheets API');
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $path = '/home/brettj/projects/laravel-inertia-chatgpt/storage/framework/sa.json';
        $client->setAuthConfig($path);

        $service = new \Google_Service_Sheets($client);

        try {

            $spreadsheet = new \Google_Service_Sheets_Spreadsheet([
                'properties' => [
                    'title' => "Testing"
                ]
            ]);
            $spreadsheet = $service->spreadsheets->create($spreadsheet, [
                'fields' => 'spreadsheetId'
            ]);
            printf("Spreadsheet ID: %s\n", $spreadsheet->spreadsheetId);
            return $spreadsheet->spreadsheetId;
        } catch (Exception $e) {
            // TODO(developer) - handle error appropriately
            echo 'Message: ' . $e->getMessage();
        }


        //echo json_encode($spreadsheet);



        // Return the document ID or any other response as needed
        return response()->json([
            'message' => 'Google Sheet created successfully',
            'document_id' => $spreadsheet,
        ]);
    }

    private function convertMarkdownToGoogleDocs($markdownContent)
    {
        // Implement your own Markdown to Google Docs conversion logic here
        // This is just a placeholder example
        $convertedContent = new \Docs\UpdateDocumentRequest();

        return $convertedContent;
    }
}
