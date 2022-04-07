<?php   include("../template/cabecera.php");   ?>

<?php

require ("../config/db.php");

$Id=(isset($_POST['Id']))?$_POST['Id']:"";
$Nom=(isset($_POST['Nom']))?$_POST['Nom']:"";
$Imagen=(isset($_FILES['Imagen']['name']))?$_FILES['Imagen']['name']:"";

$accion=(isset($_POST['accion']))?$_POST['accion']:"";



switch($accion){

    case "Agregar":

        
       $sentenciaSQL=$conexion->prepare("INSERT INTO libros ( nombre, imagen) VALUES (:nombre,:imagen);");
       $sentenciaSQL->bindParam(':nombre',$Nom);
       
       $fecha= new DateTime();
       $nombreArchivo=($Imagen!="")?$fecha->getTimestamp()."_".$_FILES["Imagen"]["name"]:"imagen.jpg";

        $tmpImagen=$_FILES["Imagen"]["tmp_name"];
        if($tmpImagen!=""){

            move_uploaded_file($tmpImagen, "../../img/".$nombreArchivo);
        }

       $sentenciaSQL->bindParam(':imagen',$nombreArchivo);
       $sentenciaSQL->execute(); 
       header("Location:docs.php");
       break;

    case "Modificar":
        $sentenciaSQL=$conexion->prepare("UPDATE libros SET nombre=:nombre WHERE id=:id");
        $sentenciaSQL->bindParam(':nombre',$Nom );
        $sentenciaSQL->bindParam(':id',$Id);
        $sentenciaSQL->execute();


        if($Imagen!==""){
            $fecha= new DateTime();
            $nombreArchivo=($Imagen!="")?$fecha->getTimestamp()."_".$_FILES["Imagen"]["name"]:"imagen.jpg";

            $tmpImagen=$_FILES["Imagen"]["tmp_name"];
            move_uploaded_file($tmpImagen, "../../img/".$nombreArchivo);

            $sentenciaSQL=$conexion->prepare("SELECT imagen FROM libros WHERE id=:id");
            $sentenciaSQL->bindParam(':id',$Id);
            $sentenciaSQL->execute();
            $libro=$sentenciaSQL->fetch(PDO::FETCH_LAZY);
    
            if(isset($libro["imagen"])&&(["iamgen"]!="imagen.jpg")){
    
                if(file_exists("../../img/".$libro["imagen"])){
    
    
                    unlink("../../img/".$libro["imagen"]);
                }
    
            }


            $sentenciaSQL=$conexion->prepare("UPDATE libros SET imagen=:imagen WHERE id=:id");
            $sentenciaSQL->bindParam(':imagen',$Imagen);
            $sentenciaSQL->bindParam(':id',$Id);
            $sentenciaSQL->execute();

        }
        header("Location:docs.php");
        break;

        

    case "Cancelar":
            header("Location:docs.php");
        
       break;


    case "Seleccionar":
        $sentenciaSQL=$conexion->prepare("SELECT * FROM libros WHERE id=:id");
        $sentenciaSQL->bindParam(':id',$Id);
        $sentenciaSQL->execute();
        $libro=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

        $Nom=$libro['nombre'];
        $Imagen=$libro['imagen'];


    
        
        break;


    case "Borrar":
        $sentenciaSQL=$conexion->prepare("SELECT imagen FROM libros WHERE id=:id");
        $sentenciaSQL->bindParam(':id',$Id);
        $sentenciaSQL->execute();
        $libro=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

        if(isset($libro["imagen"])&&(["iamgen"]!="imagen.jpg")){

            if(file_exists("../../img/".$libro["imagen"])){


                unlink("../../img/".$libro["imagen"]);
            }

        }

        $sentenciaSQL=$conexion->prepare("DELETE FROM libros WHERE id=:id");
        $sentenciaSQL->bindParam(':id',$Id);
        $sentenciaSQL->execute();
        header("Location:docs.php");
        break;
}


$sentenciaSQL=$conexion->prepare("SELECT * FROM libros");
$sentenciaSQL->execute();
$listaLibros=$sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);




?>

<div class="col-md-5">

<div class="card">
    <div class="card-header">
        Datos del libro
    </div>
    <div class="card-body">
    <form method="POST" enctype="multipart/form-data"> 
    <div class = "form-group">
        <label for="exampleInputID">ID</label>
        <input type="text"  required readonly class="form-control"value="<?php echo $Id;?>" name="Id" id="Id" placeholder="ID">
  
    </div>

    <div class = "form-group">
        <label for="exampleInputName">Nombre</label>
        <input type="text" required class="form-control" value="<?php echo $Nom;?>" name="Nom" id="Nom" placeholder="Nombre">
  
    </div>


    <div class = "form-group">
        <label for="exampleInputIMG">Imagen</label>

        <br/>
        <?php  
            if($Imagen!==""){ ?>
            <img class="img-thumbnail rounded" src="../../img/<?php echo $Imagen;?>" width="50" alt="" srcset="">

        <?php } ?>
        <input type="file"  class="form-control"  name="Imagen" id="Imagen" placeholder="Imagen">


    </div>

            <div class="btn-group" role="group" aria-label="">
                 <button type="submit" name="accion"<?php echo ($accion=="Seleccionar")?"disabled":"";?> value="Agregar" class="btn btn-success">Agregar</button>
                 <button type="submit"name="accion" <?php echo ($accion!=="Seleccionar")?"disabled":"";?>    value="Modificar" class="btn btn-warning">Modificar</button>
                 <button type="submit" name="accion" <?php echo ($accion!=="Seleccionar")?"disabled":"";?>  value="Cancelar" class="btn btn-info">Cancelar</button>
             </div>
  
   

  
  </form>
    </div>
    
</div>


 
  
  
</div>
<div class="col-md-7">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>

        <?php     foreach($listaLibros as $libro){?>
            <tr>
                <td><?php echo $libro['id']?></td>
                <td><?php echo $libro['nombre']?></td>
                <td>
                    
                    <img class="img-thumbnail rounded" src="../../img/<?php echo $libro['imagen']?>" width="50" alt="" srcset="">
                    
                </td>



                <td>
                    
                
                

                <form method="POST">

                    <input type="hidden" name="Id" id="Id" value="<?php echo $libro['id']?>">

                    <input type="submit" name="accion" value="Seleccionar" class="btn btn-primary">
                    <input type="submit" name="accion" value="Borrar" class="btn btn-danger">
                    
                </form>
            
            
            
            </td>
            </tr>
         
            <?php } ?> 
        </tbody>
    </table>
</div>

<?php   include("../template/pie.php");     ?>