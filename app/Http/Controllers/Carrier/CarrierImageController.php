<?php

namespace App\Http\Controllers\Carrier;

use App\DataTables\Carrier\CarrierImageDataTable;
use App\Http\Requests\Carrier\CreateCarrierImageRequest;
use App\Http\Requests\Carrier\UpdateCarrierImageRequest;
use App\Models\Image\CarrierImageCategory;
use App\Repositories\Carrier\CarrierImageRepository;
use Flash;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Response;

class CarrierImageController extends AppBaseController
{
    /** @var  CarrierImageRepository */
    private $carrierImageRepository;

    public function __construct(CarrierImageRepository $carrierImageRepo)
    {
        $this->carrierImageRepository = $carrierImageRepo;
    }

    /**
     * Display a listing of the CarrierImage.
     *
     * @param CarrierImageDataTable $carrierImageDataTable
     * @return mixed
     */
    public function index(CarrierImageDataTable $carrierImageDataTable)
    {
        $category = CarrierImageCategory::all();
        return $carrierImageDataTable->render('Carrier.carrier_images.index',['categories' => $category]);
    }

    /**
     * Show the form for creating a new CarrierImage.
     *
     * @return Response
     */
    public function create()
    {
        return view('Carrier.carrier_images.create');
    }

    /**
     * Store a newly created CarrierImage in storage.
     *
     * @param CreateCarrierImageRequest $request
     *
     * @return Response
     */
    public function store(CreateCarrierImageRequest $request)
    {
        $file = $request->file('file');

        $fileName = md5($file->getRealPath().time());

        $path = $file->storeAs(\WinwinAuth::carrierUser()->carrier_id.'/images',$fileName.'.'.$file->getClientOriginalExtension(),'carrier');

        $input = $request->all();

        $input['image_path'] = $path;

        $input['carrier_id'] = \WinwinAuth::carrierUser()->carrier_id;

        $input['image_size'] = $file->getSize();

        $input['uploaded_user_id'] = \WinwinAuth::carrierUser()->id;

        $this->carrierImageRepository->create($input);

        return $this->sendSuccessResponse($request);
    }

    /**
     * Display the specified CarrierImage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $carrierImage = $this->carrierImageRepository->findWithoutFail($id);

        if (empty($carrierImage)) {
            Flash::error('Carrier Image not found');

            return redirect(route('carrierImages.index'));
        }

        return view('Carrier.carrier_images.show')->with('carrierImage', $carrierImage);
    }

    /**
     * Show the form for editing the specified CarrierImage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $carrierImage = $this->carrierImageRepository->findWithoutFail($id);

        if (empty($carrierImage)) {
            Flash::error('Carrier Image not found');

            return redirect(route('carrierImages.index'));
        }

        return view('Carrier.carrier_images.edit')->with('carrierImage', $carrierImage);
    }

    /**
     * Update the specified CarrierImage in storage.
     *
     * @param  int              $id
     * @param UpdateCarrierImageRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCarrierImageRequest $request)
    {
        $carrierImage = $this->carrierImageRepository->findWithoutFail($id);

        if (empty($carrierImage)) {
            Flash::error('Carrier Image not found');

            return redirect(route('carrierImages.index'));
        }

        $carrierImage = $this->carrierImageRepository->update($request->all(), $id);

        Flash::success('Carrier Image updated successfully.');

        return redirect(route('carrierImages.index'));
    }

    /**
     * Remove the specified CarrierImage from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id,Request $request)
    {
        $carrierImage = $this->carrierImageRepository->findWithoutFail($id);

        if (empty($carrierImage)) {

            return $this->sendNotFoundResponse();
        }

        $path = $carrierImage->image_path;

        $this->carrierImageRepository->delete($id);

        \Storage::disk('carrier')->delete($path);

        return $this->sendSuccessResponse();

    }


    public function showUploadImageModal(){

        $category = CarrierImageCategory::all();
        return view('Carrier.carrier_images.image_upload')->with('category',$category);
    }

}
