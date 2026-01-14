<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />

<?php

if (isset($_FILES["imagem"]) && !empty($_FILES["imagem"])) {
    move_uploaded_file($_FILES["imagem"]["tmp_name"], "./img/" . $_FILES["imagem"]["name"]);

    echo "update realizado com sucesso";
}

?>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <form action="./upload.php" method="post" enctype="multipart/form-data">
                <label>Selecione a imagem</label>
                <input type="file" name="imagem" accept="image/*" class="form-control">
                <button type="submit" class="btn btn-success">
                    Enviar imagem
                </button>
            </form>
        </div>
    </div>
</div>





<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>