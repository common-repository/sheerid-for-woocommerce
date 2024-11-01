export default function TabContent({activeTab, tab, title, children}) {
    if (activeTab !== tab) {
        return null;
    }
    return (
        <div className={'tab-content settings-section'}>
            {title && <h3>{title}</h3>}
            {children}
        </div>
    );
}