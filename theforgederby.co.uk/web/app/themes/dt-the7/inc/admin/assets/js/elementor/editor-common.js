function elementorEditorAddOnChangeHandler(widgetType, handler) {
    widgetType = widgetType ? ":" + widgetType : "";
    elementor.channels.editor.on("change" + widgetType, handler);
}