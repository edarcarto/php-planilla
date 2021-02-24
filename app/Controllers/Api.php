<?php
/**
* AuthController
*/
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Api extends Controller
{
    protected $spreadsheet;
    public function __construct()
	{
        $this->spreadsheet = new Spreadsheet();
    }
	
    public function index()
	{
        $this->view('home');
    }
    
    public function procesar()
	{
        // $this->view('home');
        // var_dump($_FILES);exit;
        $message = "";
        if (isset($_FILES['formFile']) && $_FILES['formFile']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['formFile']['tmp_name'];
            $fileName = $_FILES['formFile']['name'];
            $fileSize = $_FILES['formFile']['size'];
            $fileType = $_FILES['formFile']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

            // $allowedfileExtensions = array('jpg', 'gif', 'png', 'zip', 'txt', 'xls', 'doc');
            $allowedfileExtensions = array('xlsx','xls');
            if (in_array($fileExtension, $allowedfileExtensions)) {
                $uploadFileDir = '../uploaded_files/';
                $dest_path = $uploadFileDir . $newFileName;
                if(move_uploaded_file($fileTmpPath, $dest_path))
                {
                    $message ='File is successfully uploaded.';
                    // procesar el excel
                    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($dest_path);
                    $spreadSheet = $reader->load($dest_path);
                    $dataAsAssocArray = $spreadSheet->getActiveSheet()->toArray();
                    $separadorCampo = ";";
                    $periodo = [];
                    $tipoIngresos = [];
                    $tipoEgresos = [];
					$numberLinesDefault = 16;
					$quantityContentDeafult = 12;
					$unidadEjecutora = [];
					$DNIS=[];
                    for ($i=0; $i < count($dataAsAssocArray); $i++) {
                        # code...
                        $data = explode("   ",$dataAsAssocArray[$i][0]);
						$searchPeriodo = strrpos($dataAsAssocArray[2][0],"PERIODO: ");
						$tmpPerido = substr($dataAsAssocArray[2][0],$searchPeriodo+9);
						//echo trim($tmpPerido);exit;
						if($dataAsAssocArray[$i][0] <> "------------------------------------------------------------------------------------------------------------------------------------------------------------"){
							//$periodo[$data[0]] = trim($searchPeriodo);
							if($i >= $numberLinesDefault){
							//echo $i."|||||||||||||||:::::::::::::||||||||||||".$numberLinesDefault."<br>";
							if($i == $numberLinesDefault + 1){
								// periodo
								$periodo[$data[0]] = explode("/",$tmpPerido);
								// unidad ejecutora
								$tmpUnidadEjecutora = explode(" ",$dataAsAssocArray[$i+10][0]);
								$unidadEjecutora[$data[0]] = $tmpUnidadEjecutora[0];
								$DNIS[$data[0]] = $tmpUnidadEjecutora[113];
								echo "|||".$DNIS[$data[0]]."|||</br>";
							}
							echo($dataAsAssocArray[$i][0])."</br>";

							}
                        }
						//$numberLinesDefault += $quantityContentDeafult;
                    }
					echo "</br>";
					// construir planillas
					$contador = 0;
					$body = "";
					foreach($periodo as $p => $v){
						$contador++;
						$body .= $unidadEjecutora[$p].$separadorCampo.trim($v[1]).$separadorCampo.trim($v[0]).$separadorCampo."01".$separadorCampo;
						$body .="01".$separadorCampo.str_pad($contador,2).$separadorCampo."10000000".$separadorCampo."100000.00".$separadorCampo."1000.00".$separadorCampo."1000.00";
						$body .=$separadorCampo."1000.00".$separadorCampo."1000.00";
						// escrtuctura del cuerpo
						$body .="</br>";
                        $body .="01".$separadorCampo;
                        $body .= $DNIS[$p].$separadorCampo;
                        $body .="00".$separadorCampo;
                        $body .="1".$separadorCampo;
                        $body .="0000".$separadorCampo;
                        $body .="000000000".$separadorCampo;
                        $body .="50,89";
					}
					echo $body;
					echo "</br>";
                }
                else
                {
                    $message = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
                }
            }
        }

        echo json_encode($message);exit;
    }
}