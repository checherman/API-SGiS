<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CatalogoPaisesSeeder::class);
        $this->call(CatalogoEstadosSeeder::class);
        $this->call(CatalogoJurisdiccionesSeeder::class);
        $this->call(CatalogoMunicipiosSeeder::class);
        $this->call(CatalogoLocalidadesSeeder::class);
        $this->call(CatalogoApoyosSeeder::class);
        $this->call(CatalogoCargosSeeder::class);
        $this->call(CatalogoTiposAltasSeeder::class);
        $this->call(CatalogoTiposItemsSeeder::class);
        $this->call(CatalogoItemsSeeder::class);
        $this->call(CatalogoTiposMediosSeeder::class);
        $this->call(CatalogoTiposNotificacionesSeeder::class);
        $this->call(CatalogoTurnosSeeder::class);
        $this->call(CatalogoUbicacionesPacienteSeeder::class);
        $this->call(CatalogoCluesSeeder::class);
        $this->call(CatalogoNivelesConesSeeder::class);
        $this->call(CatalogoCarteraServiciosSeeder::class);
        $this->call(CatalogoCarteraServiciosNivelConeSeeder::class);
        $this->call(CatalogoGruposCie10Seeder::class);
        $this->call(CatalogoCategoriasCie10Seeder::class);
        $this->call(CatalogoSubCategoriasCie10Seeder::class);
        $this->call(CatalogoDerechohabientesSeeder::class);
        $this->call(CatalogoEstadosEmbarazoSeeder::class);
        $this->call(CatalogoEstadosIncidenciasSeeder::class);
        $this->call(CatalogoEstadosPacientesSeeder::class);
        $this->call(CatalogoMetodosPlanificacionSeeder::class);
        $this->call(CatalogoParentescosSeeder::class);
        $this->call(UsuariosSeeder::class);
        $this->call(ClueUsuarioSeeder::class);
        $this->call(SisGruposSeeder::class);
        $this->call(SisModulosSeeder::class);
        $this->call(SisModulosAccionesSeeder::class);
        $this->call(SisUsuariosContactosSeeder::class);
        $this->call(SisUsuariosGruposSeeder::class);
    }
}
