<?php
    /** Home */
    Breadcrumbs::for('home', function ($trail) {
        $trail->push('Home', route('admin.home'));
    });

    /* Admin */
    Breadcrumbs::for('admin_list', function ($trail) {
        $trail->parent('home');
        $trail->push('Admin List', route('admin.users.index'));
    });

    Breadcrumbs::for('admin_add_new', function ($trail) {
        $trail->parent('admin_list');
        $trail->push('Admin Add New', route('admin.users.create'));
    });

    Breadcrumbs::for('admin_edit', function ($trail, $id) {
        $trail->parent('admin_list');
        $trail->push('Admin Edit', route('admin.users.edit', $id));
    });

    /* Station Manager */
    Breadcrumbs::for('station_manager_list', function ($trail) {
        $trail->parent('home');
        $trail->push('Station Manager List', route('admin.stationManagers.index'));
    });

    Breadcrumbs::for('station_manager_add_new', function ($trail) {
        $trail->parent('station_manager_list');
        $trail->push('Station Manager Add New', route('admin.stationManagers.create'));
    });

    Breadcrumbs::for('station_manager_edit', function ($trail, $id) {
        $trail->parent('station_manager_list');
        $trail->push('Station Manager Edit', route('admin.stationManagers.edit', $id));
    });

    /* Station */
    Breadcrumbs::for('station_list', function ($trail) {
        $trail->parent('home');
        $trail->push('Station List', route('admin.stations.index'));
    });

    Breadcrumbs::for('station_add_new', function ($trail) {
        $trail->parent('station_list');
        $trail->push('Station Add New', route('admin.stations.create'));
    });

    Breadcrumbs::for('station_edit', function ($trail, $id) {
        $trail->parent('station_list');
        $trail->push('Station Edit', route('admin.stations.edit', $id));
    });

    /* Volunteer */
    Breadcrumbs::for('volunteer_list', function ($trail) {
        $trail->parent('home');
        $trail->push('Volunteer List', route('admin.volunteers.index'));
    });

    Breadcrumbs::for('volunteer_add_new', function ($trail) {
        $trail->parent('volunteer_list');
        $trail->push('Volunteer Add New', route('admin.volunteers.create'));
    });

    Breadcrumbs::for('volunteer_edit', function ($trail, $id) {
        $trail->parent('volunteer_list');
        $trail->push('Volunteer Edit', route('admin.volunteers.edit', $id));
    });

     /* Audio */
     Breadcrumbs::for('audio_list', function ($trail) {
        $trail->parent('home');
        $trail->push('YYAT Vol List', route('admin.audios.index'));
    });

    Breadcrumbs::for('audio_add_new', function ($trail) {
        $trail->parent('audio_list');
        $trail->push('YYAT Vol Add New', route('admin.audios.create'));
    });

    Breadcrumbs::for('audio_detail', function ($trail, $id) {
        $trail->parent('audio_list');
        $trail->push('YYAT Vol Detail', route('admin.audios.show', $id));
    });

     Breadcrumbs::for('content_list', function ($trail) {
         $trail->parent('home');
         $trail->push('YYAT Vol List', route('admin.contents.index'));
     });

     Breadcrumbs::for('content_add_new', function ($trail) {
         $trail->parent('content_list');
         $trail->push('YYAT Vol Add New', route('admin.contents.create'));
     });

     Breadcrumbs::for('content_detail', function ($trail, $id) {
         $trail->parent('content_list');
         $trail->push('YYAT Vol Detail', route('admin.contents.show', $id));
     });

    /* Audio */
    // Breadcrumbs::for('audio_list', function ($trail) {
    //     $trail->parent('home');
    //     $trail->push('Audio List', route('admin.audios.index'));
    // });

    // Breadcrumbs::for('audio_add_new', function ($trail) {
    //     $trail->parent('audio_list');
    //     $trail->push('Audio Add New', route('admin.audios.create'));
    // });

    // Breadcrumbs::for('audio_detail', function ($trail, $id) {
    //     $trail->parent('audio_list');
    //     $trail->push('Audio Detail', route('admin.audios.show', $id));
    // });

    /* Programme */
    Breadcrumbs::for('programme_list', function ($trail) {
        $trail->parent('home');
        $trail->push('YYAT List', route('admin.programmes.index'));
    });

    Breadcrumbs::for('programme_add_new', function ($trail) {
        $trail->parent('programme_list');
        $trail->push('YYAT Add New', route('admin.programmes.create'));
    });

    Breadcrumbs::for('programme_edit', function ($trail, $id) {
        $trail->parent('programme_list');
        $trail->push('YYAT Edit', route('admin.programmes.edit', $id));
    });

    /* Share */
    Breadcrumbs::for('share_list', function ($trail) {
        $trail->parent('home');
        $trail->push('Share List', route('admin.shares.index'));
    });

    Breadcrumbs::for('share_audio_detail', function ($trail, $id) {
        $trail->parent('share_list');
        $trail->push('Audio Detail', route('admin.audios.show', $id));
    });

    /* Documents */
    Breadcrumbs::for('document_list', function ($trail) {
        $trail->parent('home');
        $trail->push('Document List', route('admin.documents.index'));
    });

    Breadcrumbs::for('document_add_new', function ($trail) {
        $trail->parent('document_list');
        $trail->push('Document Add New', route('admin.documents.create'));
    });

    Breadcrumbs::for('document_detail', function ($trail, $id) {
        $trail->parent('document_list');
        $trail->push('Document Detail', route('admin.documents.show', $id));
    });
