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

#ifndef IMPORT_JSON_TO_KSENMARTVIEW_H
#define IMPORT_JSON_TO_KSENMARTVIEW_H

// Qt headers
#include <QObject>

namespace KTextEditor {
class MainWindow;
}

class import_json_to_ksenmartPlugin;

class import_json_to_ksenmartView: public QObject
{
    Q_OBJECT

public:
    import_json_to_ksenmartView(import_json_to_ksenmartPlugin* plugin, KTextEditor::MainWindow *view);
    ~import_json_to_ksenmartView() override;
};

#endif // IMPORT_JSON_TO_KSENMARTVIEW_H
