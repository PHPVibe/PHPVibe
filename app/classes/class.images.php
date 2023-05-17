<?php /** Upload & Image manipulations **/
class FileUploader extends ImgTools {
	
	// *** Parameters passed in by the user
	private $fileInputName;
	private $saveFilePath;
	private $newFileName;
	private $allowedFileTypesArray = array();
	private $maxSize;
	
	// *** Parameters set by class
	private $targetPath;
	
	// *** Uploaded file information
   	private $fileTempName;
   	private $fileName;
   	private $fileSize; 
   	private $fileType;
	private $fileError;
   	private $fileExtension; 
	
	// *** This array holds errors to display to the users only.
	private $errorsArray = array();
	
	// *** This array holds errors you can use for debuggin' lovin'.
	private $debugArray = array();
	
	// *** The Holy Grail.
	private $isSuccessful;



	function __construct($fileInputName, $saveFilePath, $newFileName, $allowedFileTypesArray = array('.jpg', '.png', '.gif', '.bmp'), $maxSize = '2097152')
	
	{
		
		// *** Call me a pessimist, but I'm setting this sucker to false.
		$this->isSuccessful = false;
		
		$this->fileInputName = $fileInputName;
		$this->saveFilePath = $saveFilePath;
		$this->newFileName = $this->remove_ext($newFileName);
		$this->allowedFileTypesArray = $allowedFileTypesArray;
		$this->maxSize = $maxSize;
		
			
		$this->getUploadedFileInfo();
		$this->checkUploadedFile();	
		$this->processUploadedFile();
		
	}
	


	private function getUploadedFileInfo()
	#
	
	#	Purpose:	Get the uploaded file info provided in the PHP $_FILES array
	#
	{
       	$this->fileTempName	= $_FILES[$this->fileInputName]["tmp_name"];
       	$this->fileName		= $_FILES[$this->fileInputName]["name"];
       	$this->fileSize		= $_FILES[$this->fileInputName]["size"];
       	$this->fileType	    = $_FILES[$this->fileInputName]["type"];
       	$this->fileError    = $_FILES[$this->fileInputName]["error"];

		$this->retrieveExtension($this->fileName);
		
	}
	


	private function retrieveExtension($fileName) 
	#
	
	#	Purpose:	
	#
	{
		// *** Gets the last portion of the string starting at (and including) '.'
       	$this->fileExtension 	= strtolower(strrchr($fileName, '.'));
	}



	private function checkUploadedFile()
	#
		
	#	Purpose:	Performs tests on the file to make sure it is valid and uploaded.
	#
	{
   		// *** Test a file has been entered for upload
       	if (!$this->fileName) {
	
			$this->errorsArray[] = 'No file selected. Please select a file to upload';         
		}      


		switch ($this->fileError) {
		   case 0:
			   break;
		   case 1:
			   $this->errorsArray[] = "The uploaded file exceeds the upload_max_filesize directive (".ini_get("upload_max_filesize").") in php.ini.";
			   break;
		   case 2:
			   $this->errorsArray[] = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
			   break;
		   case 3:
			   $this->errorsArray[] = "The uploaded file was only partially uploaded.";
			   break;
		   case 4:
			   $this->errorsArray[] = "No file was uploaded.";
			   break;
		   case 5:
			   $this->errorsArray[] = "Missing a temporary folder.";
			   break;
		   case 6:
			   $this->errorsArray[] = "Failed to write file to disk.";
			   break;
		   default:
			   $this->errorsArray[] = "Unknown File Error.";
		}


		// *** Check for a valid file type
		if (!in_array($this->fileExtension,$this->allowedFileTypesArray) && empty($this->errorsArray)) {
			
			// *** Get extensions from array -> string
			$extensions = ''; 
			foreach ($this->allowedFileTypesArray as $value) {
				
				// *** Remove the '.' eg '.jpg' = 'jpg'
				$extensions .= ltrim($value, '.') . ', ';
			}
			
			// *** Remove the last comma
			$extensions = rtrim($extensions, ', ');
			
           	$this->errorsArray[] = 'The type of file you are trying to upload is invalid. Please make sure it is one of the following file types: ' . $extensions . '.';								    			
		}

       	// *** Check the size
       	if (($this->fileSize >= $this->maxSize) && empty($this->errorsArray)) {
           	$this->errorsArray[] = 'The file you are trying to uploaded is too big. Try again with a file smaller than ' . $this->maxSize .'.';
       	}

       	// *** Make sure the file was uploaded to the server
       	if (!is_uploaded_file($this->fileTempName) &&  empty($this->errorsArray)) {
           	$this->errorsArray[] = 'There was an error uploading your file. Please try again.';        
       	}		

		if (count($this->errorsArray) == 0) {
			$this->debugArray[] = 'Just checked the uploded file, all is good.';
		} else {		
			$this->debugArray[] = 'Just checked the uplaoded file. User file had some issues... check errorsArray';		
		}
		
	}



	private function processUploadedFile()
	#
		
	#	Purpose:	This is where we, ...wait for it, move the file from our hidden, underground,
	# 				temporary location, - known to only a few, to the public eye. ...The new Paris Hilton?	
	#
	# 						...POSSIBLY.
	#
	{
		// *** If no errors
		if (count($this->errorsArray) == 0) {

			// *** Check the upload directory exists, if not, desperatly try and create it.
			$result = $this->setupFolder();
			if (!$result) { return; }			
			
			// *** Set the target path. (path + filename + extension)
			$this->targetPath = $this->saveFilePath . '/' . $this->newFileName . $this->fileExtension; 

	        // *** Move file to our specified location
	        if (move_uploaded_file($this->fileTempName, $this->targetPath)) {
				$this->debugArray[] = '"move_upload_file" command successful.';

				// *** This IS the magic. Don't even think about saying its' name.	
				$this->isSuccessful = true;
				
				parent::__construct($this->targetPath);
	
			} else {
				$this->errorsArray[] = 'There was an error uploading your file. Please try again.';  
				$this->debugArray[] = 'There was an error with "move_upload_file". :('; 
			}	
		}		
	}



	private function setupFolder($permissions = 0777)
	#
	
	#	Purpose:	Creates the upload folder, with 777 permissions, if it doesn't exist already.
	# 				This is kinda a desperate attempt. Most uploading issues will stem from permissions.
	# 				
	# 				NOTE: You need permissions to for the folder you are wanting to create a new folder in.
	# 				This is unlikly to happen.
	#
	{
		$result = true;
		if (!is_dir($this->saveFilePath)) {
			if (mkdir($this->saveFilePath, $permissions)) {
								
				$this->debugArray[] = 'Folder does not exist but has been created.';

				
			} else {
				$this->debugArray[] = 'Folder does not exist and could NOT be created.';
				$result = false;
			}		
		} else {			
			$this->debugArray[] = 'Folder NOT created, it already exists!';
		}
		
		// *** Check if the path is writable
		if (!is_writable($this->saveFilePath)) {
			$this->errorsArray[] = 'The path is not writable';
			$result = false;
		}		
		
		return $result;
	}



	function remove_ext($name)
 	 {
 	     $ext = strrchr($name, '.');
 	     if($ext !== false)
 	     {
 	         $name = substr($name, 0, -strlen($ext));
 	     }
 	     return $name;
  	 }



	public function getTargetPath() 
	{
		return $this->targetPath;
	}
	 
	public function getErrorsArray()
	{
		return $this->errorsArray;
	}
	
	public function getError()
	{
		return array_pop($this->errorsArray);
	}
	
	public function getDebugArray()
	{
		return $this->debugArray;
	}
	
	public function getIsSuccessful()
	{
		return $this->isSuccessful;
	}	
	
	public function getExtension()
	{
		return $this->fileExtension;
	}
	



}


class ImgTools
{

    private $fileName;
    private $image;
    protected $imageResized;
    private $widthOriginal;			# Always be the original width
    private $heightOriginal;
    private $width;					# Current width (width after resize)
    private $height;
    private $imageSize;
	private $fileExtension;

	private $debug = true;
	private $errorArray = array();

	private $forceStretch = true;
	private $aggresiveSharpening = false;

	private $transparentArray = array('.png', '.gif');
	private $keepTransparency = true;
	private $fillColorArray = array('r'=>255, 'g'=>255, 'b'=>255);
	
	private $sharpenArray = array('jpg');
	
	private $psdReaderPath;
	private $filterOverlayPath;

	private $isInterlace;
	
	private $captionBoxPositionArray = array();
	
	private $fontDir = 'fonts';

	private $cropFromTopPercent = 10;




    function __construct($fileName)
   
	# Date:		  27-02-08		
    # Purpose:    Constructor		
    # Param in:   $fileName: File name and path.
    # Param out:  n/a
    # Reference:
    # Notes:
    #
    {
		if (!$this->testGDInstalled()) { if ($this->debug) { var_dump('The GD Library is not installed.'); }else{ var_dump(); }};

		$this->initialise();
		
        // *** Save the image file name. Only store this incase you want to display it
        $this->fileName = $fileName;
		$this->fileExtension = strtolower(strrchr($fileName, '.'));
		
        // *** Open up the file
        $this->image = $this->openImage($fileName);

		
		// *** Assign here so we don't modify the original
		$this->imageResized = $this->image;		
		
        // *** If file is an image
        if ($this->testIsImage($this->image))
        {
            // *** Get width and height
            $this->width  = imagesx($this->image);
            $this->widthOriginal = imagesx($this->image);
            $this->height = imagesy($this->image);
            $this->heightOriginal = imagesy($this->image);

		  
		    /* 	Added 15-09-08
		     *	Get the filesize using this build in method.
		     *	Stores an array of size
		     *	
		     *	$this->imageSize[1] = width
		     *	$this->imageSize[2] = height
		     *	$this->imageSize[3] = width x height
		     *		     		     
		     */
            $this->imageSize = getimagesize($this->fileName);
			
        } else {
			$this->errorArray[] = 'File is not an image';
		}
    }
	

	
	private function initialise () {
		
		$this->psdReaderPath = dirname(__FILE__) . '/classPhpPsdReader.php';
		$this->filterOverlayPath = dirname(__FILE__) . '/filters';

		// *** Set if image should be interlaced or not. 
		$this->isInterlace = false;
	}


	
/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*- 
	Resize	
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/	

	
    public function resizeImage($newWidth, $newHeight, $option = 0, $sharpen = false)
    
    {

		// *** We can pass in an array of options to change the crop position
		$cropPos = 'm';
		if (is_array($option) && strtolower($option[0]) == 'crop') {
			$cropPos = $option[1];  		   # get the crop option
		} else if (strpos($option, '-') !== false) {
			// *** Or pass in a hyphen seperated option
			$optionPiecesArray = explode('-', $option);
			$cropPos = end($optionPiecesArray);
		}
			
		// *** Check the option is valid
		$option = $this->prepOption($option);
	
		// *** Make sure the file passed in is valid
		if (!$this->image) { if ($this->debug) { var_dump('file ' . $this->getFileName() .' is missing or invalid'); }else{ var_dump(); }};

		// *** Get optimal width and height - based on $option
		$dimensionsArray = $this->getDimensions($newWidth, $newHeight, $option);

		$optimalWidth  = $dimensionsArray['optimalWidth'];
		$optimalHeight = $dimensionsArray['optimalHeight'];

		// *** Resample - create image canvas of x, y size
		$this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
		$this->keepTransparancy($optimalWidth, $optimalHeight, $this->imageResized);
		imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);


		// *** If '4', then crop too
		if ($option == 4 || $option == 'crop') {

			if (($optimalWidth >= $newWidth && $optimalHeight >= $newHeight)) { 
				$this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight, $cropPos);
			}
		}


		// *** Sharpen image (if jpg and the user wishes to do so)
		if ($sharpen && in_array($this->fileExtension, $this->sharpenArray)) {
		
			// *** Sharpen
			$this->sharpen();
		}
    }



	public function cropImage($newWidth, $newHeight, $cropPos = 'm')
   
    # Date:       08-09-11
    # Purpose:    Crops the image
    # Param in:   $newWidth: crop with 	
    #             $newHeight: crop height
	#			  $cropPos:	Can be any of the following: 
	#							tl, t, tr, l, m, r, bl, b, br, auto
	#						Or:
	#							a custom position such as '30x50'											
    # Param out:  n/a
    # Reference:
    # Notes:	  
    #
	{

		// *** Make sure the file passed in is valid
		if (!$this->image) { if ($this->debug) { var_dump('file ' . $this->getFileName() .' is missing or invalid'); }else{ var_dump(); }};

		$this->imageResized = $this->image;
		$this->crop($this->width, $this->height, $newWidth, $newHeight, $cropPos);

	}



	private function keepTransparancy($width, $height, $im)
   
    # Date:       08-04-11
    # Purpose:    Keep transparency for png and gif image  
    # Param in:
    # Param out:  n/a
    # Reference:
    # Notes:
    #
	{
		// *** If PNG, perform some transparency retention actions (gif untested)
		if (in_array($this->fileExtension, $this->transparentArray) && $this->keepTransparency) {
			imagealphablending($im, false);
			imagesavealpha($im, true);
			$transparent = imagecolorallocatealpha($im, 255, 255, 255, 127);
			imagefilledrectangle($im, 0, 0, $width, $height, $transparent);
		} else {
			$color = imagecolorallocate($im, $this->fillColorArray['r'], $this->fillColorArray['g'], $this->fillColorArray['b']);
			imagefilledrectangle($im, 0, 0, $width, $height, $color);			
		}
	}



    private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight, $cropPos)
   
    # Date:       15-09-08
    # Purpose:    Crops the image
    # Param in:   $newWidth:
    #             $newHeight:
    # Param out:  n/a
    # Reference:
    # Notes:	  
    #
    {
		
		// *** Get cropping co-ordinates
		$cropArray = $this->getCropPlacing($optimalWidth, $optimalHeight, $newWidth, $newHeight, $cropPos);
		$cropStartX = $cropArray['x'];
		$cropStartY = $cropArray['y'];

		// *** Crop this bad boy
		$crop = imagecreatetruecolor($newWidth , $newHeight);
		$this->keepTransparancy($optimalWidth, $optimalHeight, $crop);
		imagecopyresampled($crop, $this->imageResized, 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight);
		
		$this->imageResized = $crop;

		// *** Set new width and height to our variables
		$this->width = $newWidth;
		$this->height = $newHeight;		
 
    }



	private function getCropPlacing($optimalWidth, $optimalHeight, $newWidth, $newHeight, $pos='m')
	#
	
	
	#	Purpose:	Set the cropping area.
	#	Params in:
	#	Params out:	(array) the crop x and y co-ordinates.
	#	Notes:		When specifying the exact pixel crop position (eg 10x15), be
	#				very careful as it's easy to crop out of the image leaving 
	#				black borders. 
	#		
	{
		$pos = strtolower($pos);

		// *** If co-ords have been entered
		if (strstr($pos, 'x')) {
			$pos = str_replace(' ', '', $pos);
			
			$xyArray = explode('x', $pos);
			list($cropStartX, $cropStartY) = $xyArray;
			
		} else {		
	
			switch ($pos) {
				case 'tl':
					$cropStartX = 0;
					$cropStartY = 0;
					break;

				case 't':
					$cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 );
					$cropStartY = 0;				
					break;

				case 'tr':
					$cropStartX = $optimalWidth - $newWidth;
					$cropStartY = 0;				
					break;

				case 'l':
					$cropStartX = 0;
					$cropStartY = ( $optimalHeight/ 2) - ( $newHeight/2 );				
					break;

				case 'm':
					$cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 );
					$cropStartY = ( $optimalHeight/ 2) - ( $newHeight/2 );
					break;

				case 'r':
					$cropStartX = $optimalWidth - $newWidth;
					$cropStartY = ( $optimalHeight/ 2) - ( $newHeight/2 );
					break;

				case 'bl':
					$cropStartX = 0;
					$cropStartY = $optimalHeight - $newHeight;				
					break;

				case 'b':	
					$cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 );
					$cropStartY = $optimalHeight - $newHeight;
					break;

				case 'br':
					$cropStartX = $optimalWidth - $newWidth;
					$cropStartY = $optimalHeight - $newHeight;
					break;

				case 'auto':
					// *** If image is a portrait crop from top, not center. v1.5 
					if ($optimalHeight > $optimalWidth) {
						$cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 );
						$cropStartY = ($this->cropFromTopPercent /100) * $optimalHeight;
					} else {
						
						// *** Else crop from the center
						$cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 );
						$cropStartY = ( $optimalHeight/ 2) - ( $newHeight/2 );
					}
					break;

				default:
					// *** Default to center
					$cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 );
					$cropStartY = ( $optimalHeight/ 2) - ( $newHeight/2 );
					break;
			}	
		}
		
		return array('x' => $cropStartX, 'y' => $cropStartY);		
	}
	

	
	private function getDimensions($newWidth, $newHeight, $option)
   
    # Param in:   $newWidth:
    #             $newHeight:
    # Param out:  Array of new width and height values
    # Reference:
    # Notes:	  If $option = 3 then this function is call recursivly
	#
	#			  To clarify the $option input:
    #               0 = The exact height and width dimensions you set.
    #               1 = Whatever height is passed in will be the height that
    #                   is set. The width will be calculated and set automatically
    #                   to a the value that keeps the original aspect ratio.
    #               2 = The same but based on the width.
    #               3 = Depending whether the image is landscape or portrait, this
    #                   will automatically determine whether to resize via
    #                   dimension 1,2 or 0.
	#               4 = Resize the image as much as possible, then crop the
	#					remainder.
	{

        switch (strval($option))
        {
            case '0':
			case 'exact':
                $optimalWidth = $newWidth;
                $optimalHeight= $newHeight;
                break;
            case '1':
			case 'portrait':
                $dimensionsArray = $this->getSizeByFixedHeight($newWidth, $newHeight);
				$optimalWidth = $dimensionsArray['optimalWidth'];
				$optimalHeight = $dimensionsArray['optimalHeight'];
                break;
            case '2':
			case 'landscape':
                $dimensionsArray = $this->getSizeByFixedWidth($newWidth, $newHeight);
				$optimalWidth = $dimensionsArray['optimalWidth'];
				$optimalHeight = $dimensionsArray['optimalHeight'];
                break;
            case '3':
			case 'auto':
                $dimensionsArray = $this->getSizeByAuto($newWidth, $newHeight);
				$optimalWidth = $dimensionsArray['optimalWidth'];
				$optimalHeight = $dimensionsArray['optimalHeight'];
                break;
			case '4':
			case 'crop':
                $dimensionsArray = $this->getOptimalCrop($newWidth, $newHeight);
				$optimalWidth = $dimensionsArray['optimalWidth'];
				$optimalHeight = $dimensionsArray['optimalHeight'];
                break;
        }

		return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
	}



    private function getSizeByFixedHeight($newWidth, $newHeight)
    {
		// *** If forcing is off...
		if (!$this->forceStretch) {

			// *** ...check if actual height is less than target height
			if ($this->height < $newHeight) {
				return array('optimalWidth' => $this->width, 'optimalHeight' => $this->height);
			}
		}

        $ratio = $this->width / $this->height;

        $newWidth = $newHeight * $ratio;

        //return $newWidth;
		return array('optimalWidth' => $newWidth, 'optimalHeight' => $newHeight);
    }



    private function getSizeByFixedWidth($newWidth, $newHeight)
    {
		// *** If forcing is off...
		if (!$this->forceStretch) {

			// *** ...check if actual width is less than target width
			if ($this->width < $newWidth) {
				return array('optimalWidth' => $this->width, 'optimalHeight' => $this->height);
			}
		}

		$ratio = $this->height / $this->width;

        $newHeight = $newWidth * $ratio;

        //return $newHeight;
		return array('optimalWidth' => $newWidth, 'optimalHeight' => $newHeight);
    }
    


    private function getSizeByAuto($newWidth, $newHeight)
   
    # Date:       19-08-08
    # Purpose:    Depending on the height, choose to resize by 0, 1, or 2
    # Param in:   The new height and new width
    # Notes:       
    # 
    {
		// *** If forcing is off...
		if (!$this->forceStretch) {

			// *** ...check if actual size is less than target size
			if ($this->width < $newWidth && $this->height < $newHeight) {
				return array('optimalWidth' => $this->width, 'optimalHeight' => $this->height);
			}
		}

        if ($this->height < $this->width)
        // *** Image to be resized is wider (landscape)
        {
            //$optimalWidth = $newWidth;
            //$optimalHeight= $this->getSizeByFixedWidth($newWidth);

            $dimensionsArray = $this->getSizeByFixedWidth($newWidth, $newHeight);
			$optimalWidth = $dimensionsArray['optimalWidth'];
			$optimalHeight = $dimensionsArray['optimalHeight'];
        }
        elseif ($this->height > $this->width)
        // *** Image to be resized is taller (portrait)
        {
            //$optimalWidth = $this->getSizeByFixedHeight($newHeight);
            //$optimalHeight= $newHeight;

            $dimensionsArray = $this->getSizeByFixedHeight($newWidth, $newHeight);
			$optimalWidth = $dimensionsArray['optimalWidth'];
			$optimalHeight = $dimensionsArray['optimalHeight'];
        }
		else
        // *** Image to be resizerd is a square
        {

			if ($newHeight < $newWidth) {
			    //$optimalWidth = $newWidth;
				//$optimalHeight= $this->getSizeByFixedWidth($newWidth);
                $dimensionsArray = $this->getSizeByFixedWidth($newWidth, $newHeight);
				$optimalWidth = $dimensionsArray['optimalWidth'];
				$optimalHeight = $dimensionsArray['optimalHeight'];
			} else if ($newHeight > $newWidth) {
			    //$optimalWidth = $this->getSizeByFixedHeight($newHeight);
		        //$optimalHeight= $newHeight;
                $dimensionsArray = $this->getSizeByFixedHeight($newWidth, $newHeight);
				$optimalWidth = $dimensionsArray['optimalWidth'];
				$optimalHeight = $dimensionsArray['optimalHeight'];
			} else {
				// *** Sqaure being resized to a square
				$optimalWidth = $newWidth;
				$optimalHeight= $newHeight;
			}
        }

		return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }



    private function getOptimalCrop($newWidth, $newHeight)
	
	{

		// *** If forcing is off...
		if (!$this->forceStretch) {

			// *** ...check if actual size is less than target size
			if ($this->width < $newWidth && $this->height < $newHeight) {
				return array('optimalWidth' => $this->width, 'optimalHeight' => $this->height);
			}
		}

		$heightRatio = $this->height / $newHeight;
		$widthRatio  = $this->width /  $newWidth;

		if ($heightRatio < $widthRatio) {
			$optimalRatio = $heightRatio;
		} else {
			$optimalRatio = $widthRatio;
		}

		$optimalHeight = $this->height / $optimalRatio;
		$optimalWidth  = $this->width  / $optimalRatio;

		return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
	}



	private function sharpen()
   
    # Purpose:    Sharpen image
    
  
	{
		
		if (version_compare(PHP_VERSION, '5.1.0') >= 0) {

			// *** 	
			if ($this->aggresiveSharpening) { # A more aggressive sharpening solution
				
				$sharpenMatrix = array( array( -1, -1, -1 ),
							        	 array( -1, 16, -1 ),
							        	 array( -1, -1, -1 ) );
				$divisor = 8;
				$offset = 0;

				imageconvolution($this->imageResized, $sharpenMatrix, $divisor, $offset);
			}
			else # More subtle and personally more desirable
			{
				$sharpness	= $this->findSharp($this->widthOriginal, $this->width);
			
				$sharpenMatrix	= array(
					array(-1, -2, -1),
					array(-2, $sharpness + 12, -2), //Lessen the effect of a filter by increasing the value in the center cell
					array(-1, -2, -1)
				);
				$divisor		= $sharpness; // adjusts brightness
				$offset			= 0;
				imageconvolution($this->imageResized, $sharpenMatrix, $divisor, $offset);
			}
		}
		else 
		{
			if ($this->debug) { var_dump('Sharpening required PHP 5.1.0 or greater.'); }				
		}
	}

	
	
	private function sharpen2($level)
	{
			$sharpenMatrix	= array(
				array($level, $level, $level),
				array($level, (8*$level)+1, $level), //Lessen the effect of a filter by increasing the value in the center cell
				array($level, $level, $level)
			);

	}



	private function findSharp($orig, $final) 
   
    # Purpose:    Find optimal sharpness
   
	{
		$final	= $final * (750.0 / $orig);
		$a		= 52;
		$b		= -0.27810650887573124;
		$c		= .00047337278106508946;
	
		$result = $a + $b * $final + $c * $final * $final;
	
		return max(round($result), 0);
	} 


	
	private function prepOption($option)
   
    # Purpose:    Prep option like change the passed in option to lowercase
    # Param in:   (str/int) $option: eg. 'exact', 'crop'. 0, 4
    # Param out:  lowercase string
   
	{
		if (is_array($option)) {
			if (strtolower($option[0]) == 'crop' && count($option) == 2) {
				return 'crop';
			} else {
				var_dump('Crop resize option array is badly formatted.');
			}
		} else if (strpos($option, 'crop') !== false) {
			return 'crop';
		}
		
		if (is_string($option)) {
			return strtolower($option);
		}
			
		return $option;
	}

/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*- 
	Get EXIF Data	
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/		
	
	public function getExif()
   
    # Date:       07-05-2011
    # Purpose:    Get image EXIF data
    # Param in:   n/a
    # Param out:  An associate array of EXIF data
    # Reference:
    # Notes:
    #	
	{	
		// *** Check all is good - check the EXIF library exists and the file exists, too.
		if (!$this->testEXIFInstalled()) { if ($this->debug) { var_dump('The EXIF Library is not installed.'); }else{ return array(); }};
		if (!file_exists($this->fileName)) { if ($this->debug) { var_dump('Image not found.'); }else{ return array(); }};
		if ($this->fileExtension != '.jpg') { if ($this->debug) { var_dump('Metadata not supported for this image type.'); }else{ return array(); }};
		$exifData = exif_read_data($this->fileName, 'IFD0');
		
		// *** Format the apperture value
		$ev = $exifData['ApertureValue']; 
		$apPeicesArray = explode('/', $ev);
		if (count($apPeicesArray) == 2) {
			$apertureValue = round($apPeicesArray[0] / $apPeicesArray[1], 2, PHP_ROUND_HALF_DOWN) . ' EV';		
		} else { $apertureValue = '';}
		
		// *** Format the focal length
		$focalLength = $exifData['FocalLength'];
		$flPeicesArray = explode('/', $focalLength);
		if (count($flPeicesArray) == 2) {
			$focalLength = $flPeicesArray[0] / $flPeicesArray[1] . '.0 mm';
		} else { $focalLength = '';}
		
		// *** Format fNumber
		$fNumber = $exifData['FNumber'];
		$fnPeicesArray = explode('/', $fNumber);
		if (count($fnPeicesArray) == 2) {
			$fNumber = $fnPeicesArray[0] / $fnPeicesArray[1];
		} else { $fNumber = '';}
		
		// *** Resolve ExposureProgram
		if (isset($exifData['ExposureProgram'])) { $ep =  $exifData['ExposureProgram']; }
		if (isset($ep)) { $ep = $this->resolveExposureProgram($ep); }

		
		// *** Resolve MeteringMode
		$mm = $exifData['MeteringMode'];
		$mm = $this->resolveMeteringMode($mm);	
		
		// *** Resolve Flash
		$flash = $exifData['Flash'];
		$flash = $this->resolveFlash($flash);		
		

		if (isset($exifData['Make'])) { 
			$exifDataArray['make'] = $exifData['Make'];
		} else { $exifDataArray['make'] = ''; }

		if (isset($exifData['Model'])) { 
			$exifDataArray['model'] = $exifData['Model'];
		} else { $exifDataArray['model'] = ''; }
	
		if (isset($exifData['DateTime'])) { 
			$exifDataArray['date'] = $exifData['DateTime'];
		} else { $exifDataArray['date'] = ''; }

		if (isset($exifData['ExposureTime'])) {
			$exifDataArray['exposure time'] = $exifData['ExposureTime'] . ' sec.'; 
		} else { $exifDataArray['exposure time'] = ''; }

		if ($apertureValue != '') { 
			$exifDataArray['aperture value'] = $apertureValue;
		} else { $exifDataArray['aperture value'] = ''; }

		if (isset($exifData['COMPUTED']['ApertureFNumber'])) { 
			$exifDataArray['f-stop'] = $exifData['COMPUTED']['ApertureFNumber']; 
		} else { $exifDataArray['f-stop'] = ''; }
		
		if (isset($exifData['FNumber'])) { 
			$exifDataArray['fnumber'] = $exifData['FNumber']; 
		} else { $exifDataArray['fnumber'] = ''; } 

		if ($fNumber != '') { 
			$exifDataArray['fnumber value'] = $fNumber;
		} else { $exifDataArray['fnumber value'] = ''; } 

		if (isset($exifData['ISOSpeedRatings'])) {
			$exifDataArray['iso'] = $exifData['ISOSpeedRatings'];
		} else { $exifDataArray['iso'] = ''; } 

		if ($focalLength != '') { 
			$exifDataArray['focal length'] = $focalLength;
		} else { $exifDataArray['focal length'] = ''; } 

		if (isset($ep)) { 
			$exifDataArray['exposure program'] = $ep;
		} else { $exifDataArray['exposure program'] = ''; } 
	
		if ($mm != '') { 
			$exifDataArray['metering mode'] = $mm;
		} else { $exifDataArray['metering mode'] = ''; } 

		if ($flash != '') { 
			$exifDataArray['flash status'] = $flash;
		} else { $exifDataArray['flash status'] = ''; } 

		if (isset($exifData['Artist'])) { 
			$exifDataArray['creator'] = $exifData['Artist'] ;
		} else { $exifDataArray['creator'] = ''; }

		if (isset($exifData['Copyright'])) { 
			$exifDataArray['copyright'] = $exifData['Copyright']; 
		} else { $exifDataArray['copyright'] = ''; }
				
		return $exifDataArray;
	}
	
	
	
	private function resolveExposureProgram($ep)
	{
		switch ($ep) {
			case 0:	
				$ep = '';
				break;
			case 1:	
				$ep = 'manual';
				break;
			case 2:	
				$ep = 'normal program';
				break;
			case 3:	
				$ep = 'aperture priority';
				break;
			case 4:	
				$ep = 'shutter priority';
				break;
			case 5:	
				$ep = 'creative program';
				break;
			case 6:	
				$ep = 'action program';
				break;
			case 7:	
				$ep = 'portrait mode';
				break;
			case 8:	
				$ep = 'landscape mode';
				break;

			default:
				break;
		}
		
		return $ep;
	}
	
	
	
	private function resolveMeteringMode($mm)
	{
		switch ($mm) {
			case 0:	
				$mm = 'unknown';
				break;
			case 1:	
				$mm = 'average';
				break;
			case 2:	
				$mm = 'center weighted average';
				break;
			case 3:	
				$mm = 'spot';
				break;
			case 4:	
				$mm = 'multi spot';
				break;
			case 5:	
				$mm = 'pattern';
				break;
			case 6:	
				$mm = 'partial';
				break;
			case 255:	
				$mm = 'other';
				break;

			default:
				break;
		}
		
		return $mm;
	}	
	
	
	
	private function resolveFlash($flash)
	{
		switch ($flash) {
			case 0:	
				$flash = 'flash did not fire';
				break;
			case 1:	
				$flash = 'flash fired';
				break;
			case 5:	
				$flash = 'strobe return light not detected';
				break;
			case 7:	
				$flash = 'strobe return light detected';
				break;
			case 9:	
				$flash = 'flash fired, compulsory flash mode';
				break;
			case 13:	
				$flash = 'flash fired, compulsory flash mode, return light not detected';
				break;
			case 15:	
				$flash = 'flash fired, compulsory flash mode, return light detected';
				break;
			case 16:	
				$flash = 'flash did not fire, compulsory flash mode';
				break;
			case 24:	
				$flash = 'flash did not fire, auto mode';
				break;
			case 25:	
				$flash = 'flash fired, auto mode';
				break;
			case 29:	
				$flash = 'flash fired, auto mode, return light not detected';
				break;
			case 31:	
				$flash = 'flash fired, auto mode, return light detected';
				break;
			case 32:	
				$flash = 'no flash function';
				break;
			case 65:	
				$flash = 'flash fired, red-eye reduction mode';
				break;
			case 69:	
				$flash = 'flash fired, red-eye reduction mode, return light not detected';
				break;
			case 71:	
				$flash = 'flash fired, red-eye reduction mode, return light detected';
				break;
			case 73:	
				$flash = 'flash fired, compulsory flash mode, red-eye reduction mode';
				break;
			case 77:	
				$flash = 'flash fired, compulsory flash mode, red-eye reduction mode, return light not detected';
				break;
			case 79:	
				$flash = 'flash fired, compulsory flash mode, red-eye reduction mode, return light detected';
				break;
			case 89:	
				$flash = 'flash fired, auto mode, red-eye reduction mode';
				break;
			case 93:	
				$flash = 'flash fired, auto mode, return light not detected, red-eye reduction mode';
				break;
			case 95:	
				$flash = 'flash fired, auto mode, return light detected, red-eye reduction mode';
				break;

			default:
				break;
		}
		
		return $flash;
		
	}	
	
	
/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*- 
	Get IPTC Data	
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/	

	
/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*- 
	Write IPTC Data	
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/	
	
	public function writeIPTCcaption($value)
	#	Caption
	{
		$this->writeIPTC(120, $value);
	}
	
	
	
	public function writeIPTCwriter($value)
	{
		//$this->writeIPTC(65, $value);
	}	

	

	private function writeIPTC($dat, $value)
	{
		
		# LIMIT TO JPG
		
		$caption_block = $this->iptc_maketag(2, $dat, $value);
		$image_string = iptcembed($caption_block, $this->fileName);
		file_put_contents('iptc.jpg', $image_string);
	}



	private function iptc_maketag($rec,$dat,$val)
	# Author:		Thies C. Arntzen
	# Purpose:		Function to format the new IPTC text
	# Param in:		$rec: Application record. (We’re working with #2)
	#				$dat: Index. (120 for caption, 118 for contact. See the IPTC IIM 
	#					specification:
	#					http://www.iptc.org/std/IIM/4.1/specification/IIMV4.1.pdf
	#				$val: Value/data/text. Make sure this is within the length 
	#					constraints of the IPTC IIM specification
	# Ref:			http://blog.peterhaza.no/working-with-image-meta-data-in-exif-and-iptc-headers-from-php/
	#				http://php.net/manual/en/function.iptcembed.php
	#
	{
		$len = strlen($val);
		if ($len < 0x8000)
			return chr(0x1c).chr($rec).chr($dat).
			chr($len >> 8).
			chr($len & 0xff).
			$val;
		else
			return chr(0x1c).chr($rec).chr($dat).
			chr(0x80).chr(0x04).
			chr(($len >> 24) & 0xff).
			chr(($len >> 16) & 0xff).
			chr(($len >> 8 ) & 0xff).
			chr(($len ) & 0xff).
			$val;
	}	
	
	

/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*- 
	Write XMP Data	
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/		
	
	//http://xmpphptoolkit.sourceforge.net/
	
	
/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*- 
	Add Text	
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/		

	public function addText($text, $pos = '20x20', $padding = 0, $fontColor='#fff', $fontSize = 12, $angle = 0, $font = null)
   
	# Date:       18-11-09
    # Purpose:	  Add text to an image
    # Param in:   
    # Param out:  
    # Reference:  http://php.net/manual/en/function.imagettftext.php
    # Notes:      Make sure you supply the font.
    #
	{
			
		// *** Convert color
		$rgbArray = $this->formatColor($fontColor);
		$r = $rgbArray['r'];
		$g = $rgbArray['g'];
		$b = $rgbArray['b'];			
		
		// *** Get text font
		$font = $this->getTextFont($font);
				
		// *** Get text size
		$textSizeArray = $this->getTextSize($fontSize, $angle, $font, $text);
		$textWidth = $textSizeArray['width'];
		$textHeight = $textSizeArray['height'];
		
		// *** Find co-ords to place text
		$posArray = $this->calculatePosition($pos, $padding, $textWidth, $textHeight, false);		
		$x = $posArray['width'];
		$y = $posArray['height'];		
		
		$fontColor = imagecolorallocate($this->imageResized, $r, $g, $b);

		// *** Add text
		imagettftext($this->imageResized, $fontSize, $angle, $x, $y, $fontColor, $font, $text);
	}
	
	
	
	private function getTextFont($font)
	{
		// *** Font path (shou
		$fontPath =  dirname(__FILE__) . '/' . $this->fontDir;
		
		
		// *** The below is/may be needed depending on your version (see ref)
		putenv('GDFONTPATH=' . realpath('.'));

		// *** Check if the passed in font exsits...
		if ($font == null || !file_exists($font)) {
			
			// *** ...If not, default to this font.
			$font = $fontPath . '/arimo.ttf';

			// *** Check our default font exists...
			if (!file_exists($font)) {
				
				// *** If not, return false
				if ($this->debug) { var_dump('Font not found'); }else{ return false; }						
			}
		} 	
		
		return $font;
		
	}
	
	
	
	private function getTextSize($fontSize, $angle, $font, $text)
	{
		
		// *** Define box (so we can get the width)
		$box = @imageTTFBbox($fontSize, $angle, $font, $text);
				
		// ***  Get width of text from dimensions
		$textWidth = abs($box[4] - $box[0]);

		// ***  Get height of text from dimensions (should also be same as $fontSize)
		$textHeight = abs($box[5] - $box[1]);	
		
		return array('height' => $textHeight, 'width' => $textWidth);
	}


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*- 
	Add Watermark	
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/	
	
	public function addWatermark($watermarkImage, $pos, $padding = 0, $opacity = 0)
   
    # Date:       18-11-09
    # Purpose:	  Add watermark image
    # Param in:   (str) $watermark: The watermark image
	#			  (str) $pos: Could be a pre-determined position such as:
	#						tl = top left,
	#						t  = top (middle), 
	#						tr = top right,
	#						l  = left,
	#						m  = middle,
	#						r  = right,
	#						bl = bottom left,
	#						b  = bottom (middle),
	#						br = bottom right
	#					Or, it could be a co-ordinate position such as: 50x100
	#				
	#			  (int) $padding: If using a pre-determined position you can 
	#					adjust the padding from the edges by passing an amount 
	#					in pixels. If using co-ordinates, this value is ignored.
    # Param out:
    # Reference:  http://www.php.net/manual/en/image.examples-watermark.php
    # Notes:      Based on example in reference.
	#			
    #
	{
	
		// Load the stamp and the photo to apply the watermark to
		$stamp = $this->openImage ($watermarkImage);		# stamp
		$im = $this->imageResized;						# photo
		
		// *** Get stamps width and height
		$sx = imagesx($stamp);
		$sy = imagesy($stamp);		
		
		// *** Find co-ords to place image
		$posArray = $this->calculatePosition($pos, $padding, $sx, $sy);		
		$x = $posArray['width'];
		$y = $posArray['height'];

		// *** Set watermark opacity
		if (strtolower(strrchr($watermarkImage, '.')) == '.png') {

			$opacity = $this->invertTransparency($opacity, 100);
			$this->filterOpacity($stamp, $opacity);
		}

		// Copy the watermark image onto our photo
		imagecopy($im, $stamp, $x, $y, 0, 0, imagesx($stamp), imagesy($stamp));
		
	}
	
	
	
	private function calculatePosition($pos, $padding, $assetWidth, $assetHeight, $upperLeft = true)
	#
	
	#	Date:		08-05-11
	#	Purpose:	Calculate the x, y pixel cordinates of the asset to place	
	#	Params in:	(str) $pos:	Either something like: "tl", "l", "br" or an
	#					exact position like: "100x50"
	#				(int) $padding: The amount of padding from the edge. Only
	#					used for the predefined $pos.
	#				(int) $assetWidth: The width of the asset to add to the image
	#				(int) $assetHeight: The height of the asset to add to the image
	#				(bol) $upperLeft: if true, the asset will be positioned based
	#					on the upper left x, y coords. If false, it means you're
	#					using the lower left as the basepoint and this will 
	#					convert it to the upper left position 
	#	Params out:
	#	NOTE: this is done from the UPPER left corner!! But will convert lower 
	#		left basepoints to upper left if $upperleft is set to false
	#			
	#
	{
		$pos = strtolower($pos);
		
		// *** If co-ords have been entered
		if (strstr($pos, 'x')) {
			$pos = str_replace(' ', '', $pos);
			
			$xyArray = explode('x', $pos);
			list($width, $height) = $xyArray;
			
		} else {
	
			switch ($pos) {
				case 'tl':
					$width = 0 + $padding;
					$height = 0 + $padding;
					break;
				
				case 't':
					$width = ($this->width / 2) - ($assetWidth / 2);
					$height = 0 + $padding;
					break;
				
				case 'tr':
					$width = $this->width - $assetWidth - $padding; 
					$height = 0 + $padding;;
					break;
				
				case 'l':
					$width = 0 + $padding;
					$height = ($this->height / 2) - ($assetHeight / 2);
					break;
				
				case 'm':
					$width = ($this->width / 2) - ($assetWidth / 2);
					$height = ($this->height / 2) - ($assetHeight / 2);
					break;
				
				case 'r':
					$width = $this->width - $assetWidth - $padding;
					$height = ($this->height / 2) - ($assetHeight / 2);
					break;
				
				case 'bl':
					$width = 0 + $padding;
					$height = $this->height - $assetHeight - $padding;
					break;
				
				case 'b':	
					$width = ($this->width / 2) - ($assetWidth / 2);
					$height = $this->height - $assetHeight - $padding;
					break;
				
				case 'br':
					$width = $this->width - $assetWidth - $padding;
					$height = $this->height - $assetHeight - $padding;
					break;

				default:
					$width = 0;
					$height = 0;
					break;
			}	
		}
		
		if (!$upperLeft) {
			$height = $height + $assetHeight;
		}
		
		return array('width' => $width, 'height' => $height);
	}
	
	
	
	
	private function filterOpacity(&$img, $opacity = 75) 
	#
	#	Author:			aiden dot mail at freemail dot hu 
	#	Author date:	29-03-08 08:16	
	#	Date added:		08-05-11	
	#	Purpose:		Change opacity of image
	#	Params in:		$img: Image resource id
	#					(int) $opacity: the opacity amount: 0-100, 100 being not opaque. 
	#	Params out:		(bool) true on success, else false
	#	Ref:			http://www.php.net/manual/en/function.imagefilter.php#82162
	#	Notes:			png only
	#
	{
				
		if (!isset($opacity)) {
			return false;
		}
		
		if ($opacity == 100) {
			return true;
		}
		
		$opacity /= 100;

		//get image width and height
		$w = imagesx($img);
		$h = imagesy($img);

		//turn alpha blending off
		imagealphablending($img, false);

		//find the most opaque pixel in the image (the one with the smallest alpha value)
		$minalpha = 127;
		for ($x = 0; $x < $w; $x++)
			for ($y = 0; $y < $h; $y++) {
				$alpha = ( imagecolorat($img, $x, $y) >> 24 ) & 0xFF;
				if ($alpha < $minalpha) {
					$minalpha = $alpha;
				}
			}

		//loop through image pixels and modify alpha for each
		for ($x = 0; $x < $w; $x++) {
			for ($y = 0; $y < $h; $y++) {
				//get current alpha value (represents the TANSPARENCY!)
				$colorxy = imagecolorat($img, $x, $y);
				$alpha = ( $colorxy >> 24 ) & 0xFF;
				//calculate new alpha
				if ($minalpha !== 127) {
					$alpha = 127 + 127 * $opacity * ( $alpha - 127 ) / ( 127 - $minalpha );
				} else {
					$alpha += 127 * $opacity;
				}
				//get the color index with new alpha
				$alphacolorxy = imagecolorallocatealpha($img, ( $colorxy >> 16 ) & 0xFF, ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, $alpha);
				//set pixel with the new color + opacity
				if (!imagesetpixel($img, $x, $y, $alphacolorxy)) {
	
					return false;
				}
			}
		}
		
		return true;
	}
	
	

    private function openImage($file) 
   
    # Date:       27-02-08  
    # Purpose:    
    # Param in:   
    # Param out:  n/a
    # Reference: 
    # Notes:
    # 
    {
		
		if (!file_exists($file) && !$this->checkStringStartsWith('http://', $file)) { if ($this->debug) { var_dump('Image not found.'); }else{ var_dump(); }};
		
        // *** Get extension
        $extension = strrchr($file, '.');
        $extension = strtolower($extension);

        switch($extension) 
        {
            case '.jpg':
            case '.jpeg':
                $img = @imagecreatefromjpeg($file);
                break;
            case '.gif':
                $img = @imagecreatefromgif($file);
                break;
            case '.png':
                $img = @imagecreatefrompng($file);
                break;        
            case '.bmp':
                $img = @$this->imagecreatefrombmp($file);
                break;
            case '.psd':
                $img = @$this->imagecreatefrompsd($file);
                break;
			
				
            // ... etc

            default:
                $img = false;
                break;
        }

        return $img;
    }
	


	public function reset()
	#
	
	#	Date:		30-08-11
	#	Purpose:	Reset the resource (allow further editing)
	#	Params in:
	#	Params out:
	#	Notes:	
	#
	{
		$this->__construct($this->fileName);
	}	
		
	
	
    public function saveImage($savePath, $imageQuality="100")
   
    # Date:       27-02-08  
    # Purpose:    Saves the image
    # Param in:   $savePath: Where to save the image including filename:
    #             $imageQuality: image quality you want the image saved at 0-100
    # Param out:  n/a
    # Reference: 
    # Notes:	  * gif doesn't have a quality parameter
	#			  * jpg has a quality setting 0-100 (100 being the best)
    #			  * png has a quality setting 0-9 (0 being the best)
	#
	#             * bmp files have no native support for bmp files. We use a
	#				third party class to save as bmp.
    {

		// *** Perform a check or two.
		if (!is_resource($this->imageResized)) { if ($this->debug) { 
			var_dump('saveImage: This is not a resource.'); 
			}else{ 
			var_dump(); 
			}}			
		$fileInfoArray = pathInfo($savePath);
		clearstatcache();
		if (!is_writable($fileInfoArray['dirname'])) {	if ($this->debug) { var_dump('The path is not writable. Please check your permissions.'); }else{ var_dump(); }}	
		
		// *** Get extension
        $extension = strrchr($savePath, '.');
        $extension = strtolower($extension);

		$error = '';

        switch($extension)
        {
            case '.jpg':
            case '.jpeg':
				$this->checkInterlaceImage($this->isInterlace);
				if (imagetypes() & IMG_JPG) {
					imagejpeg($this->imageResized, $savePath, $imageQuality);
				} else { $error = 'jpg'; }
                break;

            case '.gif':
				$this->checkInterlaceImage($this->isInterlace);
				if (imagetypes() & IMG_GIF) {
					imagegif($this->imageResized, $savePath);
				} else { $error = 'gif'; }
                break;

            case '.png':
				// *** Scale quality from 0-100 to 0-9
				$scaleQuality = round(($imageQuality/100) * 9);

				// *** Invert qualit setting as 0 is best, not 9
				$invertScaleQuality = 9 - $scaleQuality;

				$this->checkInterlaceImage($this->isInterlace);
				if (imagetypes() & IMG_PNG) {
					 imagepng($this->imageResized, $savePath, $invertScaleQuality);
				} else { $error = 'png'; }
                break;

            case '.bmp':
				file_put_contents($savePath, $this->GD2BMPstring($this->imageResized));
			    break;

			
            // ... etc

            default:
				// *** No extension - No save.
				$this->errorArray[] = 'This file type (' . $extension . ') is not supported. File not saved.';
                break;
        }

		//imagedestroy($this->imageResized);

		// *** Display error if a file type is not supported.
		if ($error != '') {
			$this->errorArray[] = $error . ' support is NOT enabled. File not saved.';
		}       
    }



	public function displayImage($fileType = 'jpg', $imageQuality="100")
   
    # Date:       18-11-09
    # Purpose:    Display images directly to the browser
    # Param in:   The image type you want to display
    # Param out:  
    # Reference:
    # Notes:
    #
	{

		if (!is_resource($this->imageResized)) { if ($this->debug) { var_dump('saveImage: This is not a resource.'); }else{ var_dump(); }}	

        switch($fileType)
        {
            case 'jpg':
            case 'jpeg':
				header('Content-type: image/jpeg');
				imagejpeg($this->imageResized, '', $imageQuality);
                break;
            case 'gif':
				header('Content-type: image/gif');
				imagegif($this->imageResized);
                break;
            case 'png':
				header('Content-type: image/png');
				
				// *** Scale quality from 0-100 to 0-9
				$scaleQuality = round(($imageQuality/100) * 9);

				// *** Invert qualit setting as 0 is best, not 9
				$invertScaleQuality = 9 - $scaleQuality;
				
				imagepng($this->imageResized, '', $invertScaleQuality);
				break;
			case 'bmp':
				echo 'bmp file format is not supported.';
				break;

            // ... etc

            default:
				// *** No extension - No save.
                break;
        }
		

		//imagedestroy($this->imageResized);
	}
	

	
	public function setTransparency($bool)
	# Sep 2011
	{
		$this->keepTransparency = $bool;	
	}
	

	
	public function setFillColor($value)
	# Sep 2011
    # Param in:   (mixed) $value: (array) Could be an array of RGB
	#							  (str) Could be hex #ffffff or #fff, fff, ffffff
	#
	# If the keepTransparency is set to false, then no transparency is to be used.
	# This is ideal when you want to save as jpg.
	#
	# this method allows you to set the background color to use instead of
	# transparency.
	#
	{
		$colorArray = $this->formatColor($value);
		$this->fillColorArray = $colorArray;	
	}
	
	
	
	public function setCropFromTop($value)
	# Sep 2011
	{
		$this->cropFromTopPercent = $value;
	}



    public function testGDInstalled()
   
    # Date:       27-02-08
    # Purpose:    Test to see if GD is installed
    # Param in:   n/a
    # Param out:  (bool) True is gd extension loaded otherwise false
    # Reference:
    # Notes:      
    # 
    {
        if(extension_loaded('gd') && function_exists('gd_info'))
        {
            $gdInstalled = true;
        }
        else
        {
            $gdInstalled = false;
        }  

        return $gdInstalled;        
    }
	


    public function testEXIFInstalled()
   
    # Date:       08-05-11
    # Purpose:    Test to see if EXIF is installed
    # Param in:   n/a
    # Param out:  (bool) True is exif extension loaded otherwise false
    # Reference:
    # Notes:      
    # 
    {
        if(extension_loaded('exif'))
        {
            $exifInstalled = true;
        }
        else
        {
            $exifInstalled = false;
        }  

        return $exifInstalled;        
    }	



    public function testIsImage($image)
   
    # Date:       27-02-08
    # Purpose:    Test if file is an image
    # Param in:   n/a
    # Param out:  n/a
    # Reference:
    # Notes:      
    # 
    {
        if ($image) 
        {
            $fileIsImage = true;
        } 
        else
        {
            $fileIsImage = false;
        }  

        return $fileIsImage;        
    }



    public function testFunct()
   
    # Date:       27-02-08
    # Purpose:    Test Function
    # Param in:   n/a
    # Param out:  n/a
    # Reference:
    # Notes:      
    # 
    {
        echo $this->height;       
    }



    public function setForceStretch($value)
   
    # Date:       23-12-10
    # Purpose:
    # Param in:   (bool) $value
    # Param out:  n/a
    # Reference:
    # Notes:
    #
    {
        $this->forceStretch = $value;
    }



    public function setFile($fileName)
   
    # Date:       28-02-08
    # Purpose:    
    # Param in:   n/a
    # Param out:  n/a
    # Reference:
    # Notes:      
    # 
    {
        self::__construct($fileName);
    }



	public function getFileName()
   
    # Date:       10-09-08
    # Purpose:    
    # Param in:   n/a
    # Param out:  n/a
    # Reference:
    # Notes:      
    # 
    {
    	return $this->fileName;
    }



	public function getHeight()
    {
    	return $this->height;
    }



	public function getWidth()
    {
    	return $this->width;
    }



	public function getOriginalHeight()
    {
    	return $this->heightOriginal;
    }



	public function getOriginalWidth()
    {
    	return $this->widthOriginal;
    }



	public function getErrors()
   
    # Date:       19-11-09
    # Purpose:    Returns the error array
    # Param in:   n/a
    # Param out:  Array of errors
    # Reference:
    # Notes:
    #
	{
		return $this->errorArray;
	}


	
	private function checkInterlaceImage($isEnabled)
	# jpg will use progressive (they don't use interace)
	{
		if ($isEnabled) {
			imageinterlace($this->imageResized, $isEnabled);
		}
	}



	protected function formatColor($value)
   
    # Date:       09-05-11
    # Purpose:    Determine color method passed in and return color as RGB
    # Param in:   (mixed) $value: (array) Could be an array of RGB
	#							  (str) Could be hex #ffffff or #fff, fff, ffffff
    # Param out:  
    # Reference:
    # Notes:	 
    #		
	{
		$rgbArray = array();
		
		// *** If it's an array it should be R, G, B
		if (is_array($value)) {
			
			if (key($value) == 0 && count($value) == 3) {
				
				$rgbArray['r'] = $value[0];
				$rgbArray['g'] = $value[1];
				$rgbArray['b'] = $value[2];
				
			} else {
				$rgbArray = $value;	
			}
		} else if (strtolower($value) == 'transparent') {
			
			$rgbArray = array(
				'r' => 255,
				'g' => 255,
				'b' => 255,
				'a' => 127
			);
			
		} else {
			
			// *** ...Else it should be hex. Let's make it RGB
			$rgbArray = $this -> hex2dec($value);
		}
		
		return $rgbArray;
	}
	
	
	
	function hex2dec($hex) 
	# Purpose:	Convert #hex color to RGB
	{
		$color = str_replace('#', '', $hex);

		if (strlen($color) == 3) {
		  $color = $color . $color;
		}

		$rgb = array(
			'r' => hexdec(substr($color, 0, 2)),
			'g' => hexdec(substr($color, 2, 2)),
			'b' => hexdec(substr($color, 4, 2)),
			'a' => 0
		);
		return $rgb;
	}		

	
	
	private function createImageColor ($colorArray) 
	{
		$r = $colorArray['r'];
		$g = $colorArray['g'];
		$b = $colorArray['b'];				
			
		return imagecolorallocate($this->imageResized, $r, $g, $b);		
	}
	
	
	
	private function testColorExists($colorArray) 
	{
		$r = $colorArray['r'];
		$g = $colorArray['g'];
		$b = $colorArray['b'];		
		
		if (imagecolorexact($this->imageResized, $r, $g, $b) == -1) {
			return false;
		} else {
			return true;
		}
	}
	
	
	
	private function findUnusedGreen()
	# Purpose:	We find a green color suitable to use like green-screen effect.
	#			Therefore, the color must not exist in the image.
	{
		$green = 255;
		
		do {

			$greenChroma = array(0, $green, 0);		
			$colorArray = $this->formatColor($greenChroma);	
			$match = $this->testColorExists($colorArray);
			$green--;
			
		} while ($match == false && $green > 0);
		
		// *** If no match, just bite the bullet and use green value of 255
		if (!$match) {
			$greenChroma = array(0, $green, 0);
		}
		
		return $greenChroma;
	} 
	
	
	
	private function findUnusedBlue()
	# Purpose:	We find a green color suitable to use like green-screen effect.
	#			Therefore, the color must not exist in the image.
	{
		$blue = 255;
		
		do {

			$blueChroma = array(0, 0, $blue);		
			$colorArray = $this->formatColor($blueChroma);	
			$match = $this->testColorExists($colorArray);
			$blue--;
			
		} while ($match == false && $blue > 0);
		
		// *** If no match, just bite the bullet and use blue value of 255
		if (!$match) {
			$blueChroma = array(0, 0, $blue);
		}		
		
		return $blueChroma;
	}	
	
	
	
	private function invertTransparency($value, $originalMax, $invert=true)
	#	Purpose:	This does two things:
	#				1) Convert the range from 0-127 to 0-100
	#				2) Inverts value to 100 is not transparent while 0 is fully
	#				   transparent (like Photoshop)
	{
		// *** Test max range
		if ($value > $originalMax) {
			$value = $originalMax;
		}
		
		// *** Test min range
		if ($value < 0) {
			$value = 0;
		}
		
		if ($invert) {
			return $originalMax - (($value/100) * $originalMax);	
		} else {	
			return ($value/100) * $originalMax;		
		}
	}
	
	
		
	private function transparentImage($src) 
	{  
		// *** making images with white bg transparent
		$r1 = 0;
		$g1 = 255;
		$b1 = 0;
		for ($x = 0; $x < imagesx($src); ++$x) {
			for ($y = 0; $y < imagesy($src); ++$y) {
				$color = imagecolorat($src, $x, $y);
				$r = ($color >> 16) & 0xFF;
				$g = ($color >> 8) & 0xFF;
				$b = $color & 0xFF;
				for ($i = 0; $i < 270; $i++) {
					//if ($r . $g . $b == ($r1 + $i) . ($g1 + $i) . ($b1 + $i)) {
					if ($r == 0 && $g == 255 && $b == 0) {
					//if ($g == 255) {
						$trans_colour = imagecolorallocatealpha($src, 0, 0, 0, 127);
						imagefill($src, $x, $y, $trans_colour);
					}
				}
			}
		}

		return $src;
	}
	
	
	
	function checkStringStartsWith($needle, $haystack) 
	#	Check if a string starts with a specific pattern
	{
		return (substr($haystack, 0, strlen($needle))==$needle);
	}


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*- 
	BMP SUPPORT (SAVING) - James Heinrich	
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/		
	
	private function GD2BMPstring(&$gd_image)
    # Author:     James Heinrich
    # Purpose:	  Save file as type bmp
    # Param in:   The image canvas (passed as ref)
    # Param out:
    # Reference:
    # Notes:	  This code was stripped out of two external files  
	#			  (phpthumb.bmp.php,phpthumb.functions.php) and added below to 
	#			  avoid dependancies.
    #
	{
		$imageX = ImageSX($gd_image);
		$imageY = ImageSY($gd_image);

		$BMP = '';
		for ($y = ($imageY - 1); $y >= 0; $y--) {
			$thisline = '';
			for ($x = 0; $x < $imageX; $x++) {
				$argb = $this->GetPixelColor($gd_image, $x, $y);
				$thisline .= chr($argb['blue']).chr($argb['green']).chr($argb['red']);
			}
			while (strlen($thisline) % 4) {
				$thisline .= "\x00";
			}
			$BMP .= $thisline;
		}

		$bmpSize = strlen($BMP) + 14 + 40;
		// BITMAPFILEHEADER [14 bytes] - http://msdn.microsoft.com/library/en-us/gdi/bitmaps_62uq.asp
		$BITMAPFILEHEADER  = 'BM';                                    // WORD    bfType;
		$BITMAPFILEHEADER .= $this->LittleEndian2String($bmpSize, 4); // DWORD   bfSize;
		$BITMAPFILEHEADER .= $this->LittleEndian2String(       0, 2); // WORD    bfReserved1;
		$BITMAPFILEHEADER .= $this->LittleEndian2String(       0, 2); // WORD    bfReserved2;
		$BITMAPFILEHEADER .= $this->LittleEndian2String(      54, 4); // DWORD   bfOffBits;

		// BITMAPINFOHEADER - [40 bytes] http://msdn.microsoft.com/library/en-us/gdi/bitmaps_1rw2.asp
		$BITMAPINFOHEADER  = $this->LittleEndian2String(      40, 4); // DWORD  biSize;
		$BITMAPINFOHEADER .= $this->LittleEndian2String( $imageX, 4); // LONG   biWidth;
		$BITMAPINFOHEADER .= $this->LittleEndian2String( $imageY, 4); // LONG   biHeight;
		$BITMAPINFOHEADER .= $this->LittleEndian2String(       1, 2); // WORD   biPlanes;
		$BITMAPINFOHEADER .= $this->LittleEndian2String(      24, 2); // WORD   biBitCount;
		$BITMAPINFOHEADER .= $this->LittleEndian2String(       0, 4); // DWORD  biCompression;
		$BITMAPINFOHEADER .= $this->LittleEndian2String(       0, 4); // DWORD  biSizeImage;
		$BITMAPINFOHEADER .= $this->LittleEndian2String(    2835, 4); // LONG   biXPelsPerMeter;
		$BITMAPINFOHEADER .= $this->LittleEndian2String(    2835, 4); // LONG   biYPelsPerMeter;
		$BITMAPINFOHEADER .= $this->LittleEndian2String(       0, 4); // DWORD  biClrUsed;
		$BITMAPINFOHEADER .= $this->LittleEndian2String(       0, 4); // DWORD  biClrImportant;

		return $BITMAPFILEHEADER.$BITMAPINFOHEADER.$BMP;
	}



	private function GetPixelColor(&$img, $x, $y)
    # Author:     James Heinrich
    # Purpose:
    # Param in:
    # Param out:
    # Reference:
    # Notes:
    #
	{
		if (!is_resource($img)) {
			return false;
		}
		return @ImageColorsForIndex($img, @ImageColorAt($img, $x, $y));
	}



	private function LittleEndian2String($number, $minbytes=1)
    # Author:     James Heinrich	  
    # Purpose:	  BMP SUPPORT (SAVING)	
    # Param in:
    # Param out:
    # Reference:
    # Notes:
    #
	{
		$intstring = '';
		while ($number > 0) {
			$intstring = $intstring.chr($number & 255);
			$number >>= 8;
		}
		return str_pad($intstring, $minbytes, "\x00", STR_PAD_RIGHT);
	}
	

/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*- 
	BMP SUPPORT (READING)	
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/	
	
	private function ImageCreateFromBMP($filename)
    # Author:     DHKold
    # Date:		  The 15th of June 2005
	# Version:	  2.0B
    # Purpose:	  To create an image from a BMP file.
    # Param in:   BMP file to open.
    # Param out:  Return a resource like the other ImageCreateFrom functions
    # Reference:  http://us3.php.net/manual/en/function.imagecreate.php#53879
	# Bug fix:		Author:		domelca at terra dot es
	#				Date:		06 March 2008
	#				Fix:		Correct 16bit BMP support
    # Notes:
	#
	{

		//Ouverture du fichier en mode binaire
		if (! $f1 = fopen($filename,"rb")) return FALSE;

		//1 : Chargement des ent�tes FICHIER
		$FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
		if ($FILE['file_type'] != 19778) return FALSE;

		//2 : Chargement des ent�tes BMP
		$BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
					 '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
					 '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
		$BMP['colors'] = pow(2,$BMP['bits_per_pixel']);

		if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];

		$BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
		$BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
		$BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
		   $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
		$BMP['decal'] = 4-(4*$BMP['decal']);

		if ($BMP['decal'] == 4) $BMP['decal'] = 0;

		//3 : Chargement des couleurs de la palette
		$PALETTE = array();
		if ($BMP['colors'] < 16777216)
		{
			$PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
		}

		//4 : Cr�ation de l'image
		$IMG = fread($f1,$BMP['size_bitmap']);
		$VIDE = chr(0);

		$res = imagecreatetruecolor($BMP['width'],$BMP['height']);
		$P = 0;
		$Y = $BMP['height']-1;
		while ($Y >= 0)
		{
			$X=0;
			while ($X < $BMP['width'])
			{
				if ($BMP['bits_per_pixel'] == 24)
					$COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
				elseif ($BMP['bits_per_pixel'] == 16)
				{

					/*
					 * BMP 16bit fix
					 * =================
					 *
					 * Ref: http://us3.php.net/manual/en/function.imagecreate.php#81604
					 *
					 * Notes:
					 * "don't work with bmp 16 bits_per_pixel. change pixel
					 * generator for this."
					 *
					 */

					// *** Original code (don't work)
					//$COLOR = unpack("n",substr($IMG,$P,2));
					//$COLOR[1] = $PALETTE[$COLOR[1]+1];

					$COLOR = unpack("v",substr($IMG,$P,2));
					$blue = ($COLOR[1] & 0x001f) << 3;
					$green = ($COLOR[1] & 0x07e0) >> 3;
					$red = ($COLOR[1] & 0xf800) >> 8;
					$COLOR[1] = $red * 65536 + $green * 256 + $blue;

				}
				elseif ($BMP['bits_per_pixel'] == 8)
				{
					$COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				}
				elseif ($BMP['bits_per_pixel'] == 4)
				{
					$COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
					if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				}
				elseif ($BMP['bits_per_pixel'] == 1)
				{
					$COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
					if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
					elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
					elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
					elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
					elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
					elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
					elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
					elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				}
				else
					return FALSE;

				imagesetpixel($res,$X,$Y,$COLOR[1]);
				$X++;
				$P += $BMP['bytes_per_pixel'];
			}

			$Y--;
			$P+=$BMP['decal'];
		}

		//Fermeture du fichier
		fclose($f1);

		return $res;
	}


/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*- 
	PSD SUPPORT (READING)	
*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-**-*-*-*-*-*-*-*-*-*-*-*-*-*/		
	
	private function imagecreatefrompsd($fileName) 
	# Author:     Tim de Koning  
	# Version:	  1.3
	# Purpose:	  To create an image from a PSD file.
	# Param in:   PSD file to open.
	# Param out:  Return a resource like the other ImageCreateFrom functions
	# Reference:  http://www.kingsquare.nl/phppsdreader
	# Notes:
	#
	{	
		if (file_exists($this->psdReaderPath)) {


			include_once($this->psdReaderPath);

			$psdReader = new PhpPsdReader($fileName);

			if (isset($psdReader->infoArray['error'])) return '';
			else return $psdReader->getImage();
		} else {
			return false;
		}
	}	



    public function __destruct() {
		if (is_resource($this->imageResized)) {
			imagedestroy($this->imageResized);
		}
	}	
	
	
}
?>