<?php

namespace App\Http\Controllers;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Docs;
use Google\Service\Docs\Document;
use Illuminate\Http\Request;

class GoogleDocController extends Controller
{
    public function createGoogleDoc(Request $request)
    {
        $client = new \Google_Client();
        $client->setApplicationName('Google Docs API');
        $client->setScopes([Docs::DOCUMENTS]);
        $client->setScopes(Drive::DRIVE);
        $client->setAccessType('offline');
        $path = '/home/brettj/projects/dekode-gpt/storage/framework/sa.json';
        $client->setAuthConfig($path);

        $service = new Docs($client);

        $title = $request->input('title');
        $document = new \Google\Service\Docs\Document(array(
            'title' => $title
        ));

        $document = $service->documents->create($document);

        $drive = new \Google\Service\Drive($client);
        $resource = new \Google\Service\Drive\Permission([
            'type' => 'user',
            'role' => 'writer',
            'emailAddress' => 'brettj@dekode.co.nz',
        ]);

        $drive->permissions->create($document->documentId, $resource, array('fields' => 'id'));


        $requests = array();
        $requests[] = new \Google\Service\Docs\Request(array(
            'insertText' => array(
                'text' => $request->input('content'),
                'location' => array(
                    'index' => 1,
                ),
            )
        ));

        $batchUpdateRequest = new \Google\Service\Docs\BatchUpdateDocumentRequest(array(
            'requests' => $requests
        ));

        $service->documents->batchUpdate($document->documentId, $batchUpdateRequest);

        \Session::put('success', [
            'color' => 'green',
            'title' => 'Success',
            'message' => 'message'
        ]);
        return back();
    }

    private function convertMarkdownToGoogleDocs($markdownContent)
    {
        // Implement your own Markdown to Google Docs conversion logic here
        // This is just a placeholder example
        $convertedContent = new \Docs\UpdateDocumentRequest();

        return $convertedContent;
    }
    public function createGoogleSheet(Request $request)
    {
        $client = new \Google_Client();
        $client->setApplicationName('Google Sheets API');
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setScopes(Drive::DRIVE);

        $client->setAccessType('offline');
        $path = '/home/brettj/projects/dekode-gpt/storage/framework/sa.json';
        $client->setAuthConfig($path);


        $service = new \Google_Service_Sheets($client);





        try {

            $spreadsheet = new \Google_Service_Sheets_Spreadsheet([
                'properties' => [
                    'title' => "Testing DeKodeGPT yay v2?"
                ]
            ]);
            $spreadsheet = $service->spreadsheets->create($spreadsheet, [
                'fields' => 'spreadsheetId'
            ]);
            //printf("Spreadsheet ID: %s\n", $spreadsheet->spreadsheetId);
            //return $spreadsheet->spreadsheetId;
        } catch (\Exception $e) {
            // TODO(developer) - handle error appropriately
            echo 'Message: ' . $e->getMessage();
        }

        $range = "Sheet1!A4:C";
        $valueRange = new \Google_Service_Sheets_ValueRange();
        $options = ['valueInputOption' => 'USER_ENTERED'];
        $valueRange->setValues(["values" => ["a", "b", "C", "D", "E"]]);
        $service->spreadsheets_values->update($spreadsheet->spreadsheetId, $range, $valueRange, $options);


        try {



            $drive = new \Google\Service\Drive($client);

            $resource = new \Google\Service\Drive\Permission([
                'type' => 'user',
                'role' => 'writer',
                'emailAddress' => 'brettj@dekode.co.nz', // folder will be shared with this email
            ]);

            $result = $drive->permissions->create($spreadsheet->spreadsheetId, $resource, array('fields' => 'id'));
        } catch (\Exception $e) {
            echo 'Message: ' . $e->getMessage();
        }

        return response()->json([
            'message' => 'Google Sheet created successfully',
            'document_id' => $spreadsheet,
        ]);
    }
}
