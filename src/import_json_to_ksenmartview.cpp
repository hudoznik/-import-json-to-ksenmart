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

#include "import_json_to_ksenmartview.h"

#include "import_json_to_ksenmartplugin.h"

// KF headers
#include <KTextEditor/Document>
#include <KTextEditor/View>
#include <KTextEditor/MainWindow>

#include <KLocalizedString>


import_json_to_ksenmartView::import_json_to_ksenmartView(import_json_to_ksenmartPlugin* plugin, KTextEditor::MainWindow* mainwindow)
    : QObject(mainwindow)
{
    Q_UNUSED(plugin);
}

import_json_to_ksenmartView::~import_json_to_ksenmartView()
{
}
