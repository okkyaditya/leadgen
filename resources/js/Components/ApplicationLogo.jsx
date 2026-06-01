export default function ApplicationLogo(props) {
    return (
        <img 
            src="/images/icons8-magnet-96.png" 
            alt="Lead Magnet Logo" 
            {...props} 
            className={`object-contain ${props.className || 'h-16 w-16'}`}
        />
    );
}
