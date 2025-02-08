<?php
namespace controller;

use DAL\BookmarkDAL;
use model\base\Bookmark;
use utils\Utility;
use model\genericmodel\GenericResponse;
use model\genericmodel\IdModel;

class BookmarkController
{
    public function __construct(private BookmarkDAL $bookmarkDAL)
    {
    }
    
    
    public function processRequest(string $method, ?string $id): void
    {
        if ($method != "POST")  
        { 
            Utility:: errorNotFound();
        }

        switch ($id)  
        {
            case "create":
                $this->createBookmark();
                break;

            case "delete":
                $this->deleteBookmark(); 
                break;

            case "getbookmark":
                $this->getBookmarkId(); 
                break;

            default:
                Utility:: errorNotFound();

        }
    }

    public function createBookmark(): void
    {
        $data = Utility:: getRequestBody(IdModel::class);
        
        //validate
        $userId = Utility:: getUserId();
        if (!isset($userId))
        {
            //error
            echo json_encode(new GenericResponse(false, "user not logged in", null, null));
            exit;
        }

        $bookmarkId = Utility:: generateUUID();
        $bookmark = new Bookmark($bookmarkId, $data->id, $userId, null);
        $this->bookmarkDAL->createBookmark($bookmark);
                
        $response = new GenericResponse(true, null, null, new IdModel($bookmarkId));
        echo json_encode($response);
    }

    public function deleteBookmark(): void
    {
        $data = Utility:: getRequestBody(IdModel::class);

        $this->bookmarkDAL->deleteBookmark($data->id);
                
        $response = new GenericResponse(true, null, null, null);
        echo json_encode($response);
    }

    public function getBookmarkId(): void
    {
        $data = Utility:: getRequestBody(IdModel::class);

        $userId = Utility:: getUserId();
        if (!isset($userId))
        {
            //error
            echo json_encode(new GenericResponse(false, "user not logged in", null, null));
            exit;
        }

        $bookmark = $this->bookmarkDAL->GetBookmarkId($userId, $data->id);
                
        $response = new GenericResponse(true, null, null, $bookmark);
        echo json_encode($response);
    }
    
}









