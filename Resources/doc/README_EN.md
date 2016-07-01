AdminCrudBundle
===============
[![Build Status](https://api.travis-ci.org/MWSimple/AdminCrudBundle.svg?branch=version30)](https://travis-ci.org/MWSimple/AdminCrudBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4bd204f1-2be2-4022-8a2e-6b70c0065cba/mini.png)](https://insight.sensiolabs.com/projects/4bd204f1-2be2-4022-8a2e-6b70c0065cba)
[![Coverage Status](https://coveralls.io/repos/github/MWSimple/AdminCrudBundle/badge.svg?branch=version30)](https://coveralls.io/github/MWSimple/AdminCrudBundle?branch=version30)
[![Latest Stable Version](https://poser.pugx.org/mwsimple/admin-crud/version)](https://packagist.org/packages/mwsimple/admin-crud) [![Total Downloads](https://poser.pugx.org/mwsimple/admin-crud/downloads)](https://packagist.org/packages/mwsimple/admin-crud) [![composer.lock available](https://poser.pugx.org/mwsimple/admin-crud/composerlock)](https://packagist.org/packages/mwsimple/admin-crud)
<sup><kbd>**SOPORTA SYMFONY 3.x**</kbd></sup>

<b>[ES](README.md) / [EN](Resources/doc/README_EN.md)</b>

Description
-----------

The mwsimple:generate:admincrud generates a very basic controller for a given entity located in a given bundle. This controller extend the default controller implements [paginator], [filter] and allows to perform the [five basic operations] on a model, allows rewriting actions and views.

    Listing all records,
    Showing one given record identified by its primary key,
    Creating a new record,
    Editing an existing record,
    Deleting an existing record.

##<p align="right">Previews</p>

<img src="https://raw.githubusercontent.com/MWSimple/AdminCrudBundle/version30/Resources/doc/preview_list.png" alt="Listar" width="50%" align="right" />
<img src="https://raw.githubusercontent.com/MWSimple/AdminCrudBundle/version30/Resources/doc/preview_new.png" alt="Crear" width="50%" align="right" />

Documentation
-------------

* [Installation](Resources/doc/instalacion_en.md)

* [Documentation](Resources/doc/documentacion_en.md) a continuación se detalla las funcionalidades para el uso correcto del Admin Crud Bundle, y poder lograr una excelente administración de su aplicación.

* [Uploads files](Resources/doc/subirarchivos_en.md)

## Dependencias

El bundle extiende de    [SensioGeneratorBundle](https://github.com/sensio/SensioGeneratorBundle) .
Para el menu             [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle) .
Para el paginador        [KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle) .
Para los filtros         [LexikFormFilterBundle](https://github.com/lexik/LexikFormFilterBundle) .
Formulario datetime type [DatetimepickerBundle](https://github.com/lexik/LexikFormFilterBundle) .
Subir archivos           [VichUploaderBundle](https://github.com/dustin10/VichUploaderBundle) .

## Author
Gonzalo Alonso - gonkpo@gmail.com

![Argentina](http://www.messentools.com/images/emoticones/banderas/MessenTools.com-Flag-of-Argentina.png "Argentina")

## Soporte
[Tecspro](http://www.tecspro.com.ar)
