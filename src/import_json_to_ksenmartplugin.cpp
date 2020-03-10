/*
 *   Copyright (C) %{CURRENT_YEAR} by %{AUTHOR} <%{EMAIL}>
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the
 *   Free Software Foundation, Inc.,
 *   51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA .
 */

#include "import_json_to_ksenmartplugin.h"

#include "import_json_to_ksenmartview.h"

// KF headers
#include <KTextEditor/MainWindow>

#include <KPluginFactory>
#include <KLocalizedString>

K_PLUGIN_FACTORY_WITH_JSON(import_json_to_ksenmartPluginFactory, "import_json_to_ksenmart.json", registerPlugin<import_json_to_ksenmartPlugin>();)


import_json_to_ksenmartPlugin::import_json_to_ksenmartPlugin(QObject* parent, const QVariantList& /*args*/)
    : KTextEditor::Plugin(parent)
{
}

import_json_to_ksenmartPlugin::~import_json_to_ksenmartPlugin()
{
}

QObject* import_json_to_ksenmartPlugin::createView(KTextEditor::MainWindow* mainwindow)
{
    return new import_json_to_ksenmartView(this, mainwindow);
}


// needed for K_PLUGIN_FACTORY_WITH_JSON
#include <import_json_to_ksenmartplugin.moc>
