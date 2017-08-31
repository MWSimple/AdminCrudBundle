AdminCrudBundle
===============
[![Build Status](https://api.travis-ci.org/MWSimple/AdminCrudBundle.svg?branch=version30)](https://travis-ci.org/MWSimple/AdminCrudBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4bd204f1-2be2-4022-8a2e-6b70c0065cba/mini.png)](https://insight.sensiolabs.com/projects/4bd204f1-2be2-4022-8a2e-6b70c0065cba)
[![Coverage Status](https://coveralls.io/repos/github/MWSimple/AdminCrudBundle/badge.svg?branch=version30)](https://coveralls.io/github/MWSimple/AdminCrudBundle?branch=version30)
[![Latest Stable Version](https://poser.pugx.org/mwsimple/admin-crud/version)](https://packagist.org/packages/mwsimple/admin-crud) [![Total Downloads](https://poser.pugx.org/mwsimple/admin-crud/downloads)](https://packagist.org/packages/mwsimple/admin-crud) [![composer.lock available](https://poser.pugx.org/mwsimple/admin-crud/composerlock)](https://packagist.org/packages/mwsimple/admin-crud)
<sup><kbd>**SOPORTA SYMFONY 3.x**</kbd></sup>

<b>[ES](https://github.com/MWSimple/AdminCrudBundle/blob/version30/README.md) / [EN](README_EN.md)</b>

Description
-----------

The mwsimple:generate:admincrud generates a very basic controller for a given entity located in a given bundle. This controller extend the default controller implements [paginator], [filter] and allows to perform the [five basic operations List, Show, New, Edit, Delete] on a model, allows rewriting actions and views.

This package was born inspired by [jordillonch/CrudGeneratorBundle](https://github.com/jordillonch/CrudGeneratorBundle) By Jordi Llonch

##<p align="right">Previews</p>

<img src="https://raw.githubusercontent.com/MWSimple/AdminCrudBundle/version30/Resources/doc/preview_list.png" alt="Listar" width="50%" align="right" />
<img src="https://raw.githubusercontent.com/MWSimple/AdminCrudBundle/version30/Resources/doc/preview_new.png" alt="Crear" width="50%" align="right" />

Demo Github
----
[https://github.com/gonzakpo/DemoAdminCrudBundle](https://github.com/gonzakpo/DemoAdminCrudBundle)

Documentation
-------------

* [Installation](instalacion_en.md)

* [Generate CRUD](generacion_en.md)

* [Configuration](configuracion_en.md)

* [Controller](controlador_en.md)

* [Views](vistas_en.md)

* [Forms](forms_en.md)

* [Forms Embed](formsembed_en.md)

* [Security](seguridad_en.md)

* [Uploads files](subirarchivos_en.md)

## Dependencies

- This bundle extends [SensioGeneratorBundle](https://github.com/sensio/SensioGeneratorBundle) .
- Menu using          [KnpMenuBundle](https://github.com/KnpLabs/KnpMenuBundle) .
- Paginator using     [KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle) .
- Filter using        [LexikFormFilterBundle](https://github.com/lexik/LexikFormFilterBundle) .
- Form datetime type  [DatetimepickerBundle](https://github.com/stephanecollot/DatetimepickerBundle) .
- Uploads files       [VichUploaderBundle](https://github.com/dustin10/VichUploaderBundle) .
- Text editor         [IvoryCKEditorBundle](https://github.com/egeloen/IvoryCKEditorBundle) .
- Image manipulation  [LiipImagineBundle](https://github.com/liip/LiipImagineBundle) .
- Twig Extensions     [TwigExtensions](http://twig.sensiolabs.org/doc/extensions/intl.html) .
- Select 2 Entity     [select2entity-bundle](https://github.com/tetranz/select2entity-bundle) .
- Symfony Collection  [symfony-collection](https://github.com/ninsuo/symfony-collection) .
- Alert Toastr        [toastr](https://github.com/CodeSeven/toastr) .
- Validator Js        [bootstrap-validator](http://1000hz.github.io/bootstrap-validator/) .

## Author
Gonzalo Alonso - gonkpo@gmail.com

## Supports
[Tecspro](http://www.tecspro.com.ar)
