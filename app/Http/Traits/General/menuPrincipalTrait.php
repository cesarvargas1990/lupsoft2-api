<?php


namespace App\Http\Traits\General;

use DB;
use App\Psusuperfil;



trait menuPrincipalTrait
{

    public function getDatosMenu($idUser)
    {

        return DB::select('SELECT m.*
                            FROM psperfil vp,
                            psusperfil up,
                            psmenu m,
                            users u
                            where vp.id = up.id_perfil
                            and m.id_perfil = vp.id
                            and vp.ind_activo = 1
                            and m.ind_activo = 1
                            and u.ind_activo = 1
                            and u.id = up.id_user
                            and u.id = :id
                            order by  m.orden asc, id_mpadre desc', array(

            'id' => $idUser

        ));

    }

    public function perfilAccion($idUser, Psusuperfil $psusuperfil)
    {
        try {
        
            $acciones = $psusuperfil::where('id_user', $idUser)
                                ->join('psperfilaccion as p', 'psusperfil.id_perfil', '=', 'p.id_perfil')
                                ->select('p.nom_accion')
                                ->get()
                                ->pluck('nom_accion')
                                ->toArray();

        
            return $acciones;

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errorCode' => $e->getCode(),
                'lineError' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    public function hacerMenuUsuario($datosMenu, $parent_id = 0)
    {
        $temp_array = array();
        foreach ($datosMenu as $element) {

            if ($element->id_mpadre == $parent_id) {
                $children = $this->hacerMenuUsuario($datosMenu, $element->id);

                if ($children) {
                    $menuItem = array(
                        'id' => $element->id,
                        'displayName' => $element->nombre,
                        'iconName' => $element->icono,
                        'route' => $element->ruta,
                        'children' => $this->hacerMenuUsuario($datosMenu, $element->id)
                    );

                } else {
                    $menuItem = array(
                        'id' => $element->id,
                        'displayName' => $element->nombre,
                        'iconName' => $element->icono,
                        'route' => $element->ruta
                    );
                }
                $temp_array[] = $menuItem;
            }
        }
        return $temp_array;
    }


}
