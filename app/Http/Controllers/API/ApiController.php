<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use App\Http\Controllers\Controller;
use App\Models\UserLog;

class ApiController extends Controller
{

	protected $_statusCode = IlluminateResponse::HTTP_OK;
	protected $maxLimit = 50;
	protected $defaultLimit = 10;


	/**
	 * @return mixed
	 */
	public function getStatusCode() {
		return $this->_statusCode;
	}

	/**
	 * @param mixed $statusCode
	 */
	public function setStatusCode($statusCode) {
		$this->_statusCode = $statusCode;
		return $this;
	}

	public function respond($data, $headers = [])
	{
		return \Response::json($data, $this->getStatusCode(), $headers);
	}

	public function respondWithSuccess($message = 'Success.'){

		return $this->setStatusCode(IlluminateResponse::HTTP_OK)->respond([
			'message' => $message,
			'status_code' => $this->getStatusCode()
		]);
	}

	public function respondWithError($message){

		return $this->respond([
			'error' => [
				'message' => $message,
				'status_code' => $this->getStatusCode()
			]
		]);
	}

	public function respondWithPagination(Paginator $paginator, $data, $extra = null)
	{

		$data = array_merge( ['data' => $data ], [
			'paginator' => [
				'total_count'	=> $paginator->total(),
				'last_page'		=> $paginator->lastPage(),
				'current_page'	=> $paginator->currentPage(),
				'limit'			=> $paginator->perPage(),
			]
        ]);
        if($extra){
            $data = array_merge($data, $extra);
        }
		return $this->respond($data);
	}
	public function respondCreated($message = 'Resource created successfully.')
	{
		return $this->setStatusCode(IlluminateResponse::HTTP_CREATED)->respond([
			'message' => $message
		]);
	}
	public function respondCreatedWithData($message = 'Resource created successfully.', $data = [])
	{
		return $this->setStatusCode(IlluminateResponse::HTTP_CREATED)->respond([
			'message' => $message,
			'data' => $data
		]);
	}

	public function respondNotFound($message = 'Not Found!')
	{
		return $this->setStatusCode(IlluminateResponse::HTTP_NOT_FOUND)->respondWithError($message);
	}

	public function respondForbidden($message = 'Forbidden.')
	{
		return $this->setStatusCode(IlluminateResponse::HTTP_FORBIDDEN)->respondWithError($message);
	}

	public function respondInternalServerError($message = 'Internal Server Error')
	{
		return $this->setStatusCode(IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR)->respondWithError($message);
	}

	public function respondNotAllowed($message = 'Method not allowed')
	{
		return $this->setStatusCode(IlluminateResponse::HTTP_METHOD_NOT_ALLOWED)->respondWithError($message);
	}

    public function respondBadRequest($message = 'Bad request')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_BAD_REQUEST)->respondWithError($message);
    }

    public function respondConflict($message = 'Conflict')
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_CONFLICT)->respondWithError($message);
    }

	public function respondValidationFailed($message = 'Validation Failed!', $validationErrors = [])
	{
		return $this->setStatusCode(IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY)->respond([
			'error' => [
				'message' => $message,
				'validation_errors' => $validationErrors,
				'status_code' => $this->getStatusCode()
			]
		]);
	}

	protected function limit()
	{
		$limit = \Input::get('limit') ?: $this->defaultLimit;

		if($limit < 1){
			$limit = 1;
		}
		elseif($limit > $this->maxLimit){
			$limit = $this->maxLimit;
		}

		return $limit;
	}

    protected function getCurrentUser()
    {
        return $account = \Auth::user();
    }

    protected function getCurrentUserAccount()
    {
        return $group = \Auth::user();
        // return $account = $group ? $group->account : null;
    }

    protected function log($event){
        $user = \Auth::user();
        if($user) {
            return UserLog::log($user, $event);
        } else {
            return false;
        }
    }
}
