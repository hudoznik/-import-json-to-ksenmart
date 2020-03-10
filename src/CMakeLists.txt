add_definitions(-DTRANSLATION_DOMAIN=\"import_json_to_ksenmart\")

set(import_json_to_ksenmart_SRCS
    import_json_to_ksenmartplugin.cpp
    import_json_to_ksenmartview.cpp
)

add_library(import_json_to_ksenmart MODULE ${import_json_to_ksenmart_SRCS})

target_link_libraries(import_json_to_ksenmart
    KF5::TextEditor
    KF5::I18n
)

install(TARGETS import_json_to_ksenmart DESTINATION ${KDE_INSTALL_PLUGINDIR}/ktexteditor)
