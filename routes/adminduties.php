<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminDuties;

Route::group(['middleware' => ['auth:admin'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
	//навыки
	Route::get('/skills', [AdminDuties::class, 'skillView'])->name('skills');
	Route::post('/edit-skill', [AdminDuties::class, 'editSkill'])->name('edit-skill');
	Route::post('/delete-skill', [AdminDuties::class, 'deleteSkill'])->name('delete-skill');
	Route::post('/add-skill', [AdminDuties::class, 'addSkill'])->name('add-skill');

	//сферы
	Route::get('/spheres', [AdminDuties::class, 'sphereView'])->name('spheres');
	Route::post('/edit-sphere', [AdminDuties::class, 'editSphere'])->name('edit-sphere');
	Route::post('/delete-sphere', [AdminDuties::class, 'deleteSphere'])->name('delete-sphere');
	Route::post('/add-sphere', [AdminDuties::class, 'addSphere'])->name('add-sphere');

	//подсферы
	Route::get('/subspheres', [AdminDuties::class, 'subsphereView'])->name('subspheres');
	Route::post('/edit-subsphere', [AdminDuties::class, 'editSubsphere'])->name('edit-subsphere');
	Route::post('/delete-subsphere', [AdminDuties::class, 'deleteSubsphere'])->name('delete-subsphere');
	Route::post('/add-subsphere', [AdminDuties::class, 'addSubsphere'])->name('add-subsphere');

	//профессии
	Route::get('/professions', [AdminDuties::class, 'professionView'])->name('professions');
	Route::post('/edit-profession', [AdminDuties::class, 'editProfession'])->name('edit-profession');
	Route::post('/delete-profession', [AdminDuties::class, 'deleteProfession'])->name('delete-profession');
	Route::post('/add-profession', [AdminDuties::class, 'addProfession'])->name('add-profession');

	Route::get('/statistics', [AdminDuties::class, 'statisticsView'])->name('statistics');
});
