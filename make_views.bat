@echo off
echo Criando views...

mkdir resources\views\home
echo. > resources\views\home\index.blade.php

mkdir resources\views\galerias
echo. > resources\views\galerias\index.blade.php
echo. > resources\views\galerias\show.blade.php

mkdir resources\views\fotos
echo. > resources\views\fotos\index.blade.php
echo. > resources\views\fotos\show.blade.php

mkdir resources\views\carrinho
echo. > resources\views\carrinho\index.blade.php

mkdir resources\views\inventario
echo. > resources\views\inventario\index.blade.php
echo. > resources\views\inventario\show.blade.php

mkdir resources\views\favoritos
echo. > resources\views\favoritos\index.blade.php

mkdir resources\views\perfil
echo. > resources\views\perfil\index.blade.php
echo. > resources\views\perfil\edit.blade.php

echo Views criadas com sucesso!
pause
