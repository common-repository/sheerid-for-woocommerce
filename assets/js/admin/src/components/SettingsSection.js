export default function SettingsSection({title = null, children}) {
    return (
        <div className={'settings-section'}>
            {title && <h3>{title}</h3>}
            {children}
        </div>
    )
}