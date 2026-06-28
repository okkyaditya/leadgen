export default function ApplicationLogo(props) {
    return (
        <img 
            src="/images/favicon_io/android-chrome-192x192.png" 
            alt="Leads Tracker Logo" 
            {...props} 
            className={`object-contain ${props.className || 'h-16 w-16'}`}
        />
    );
}
